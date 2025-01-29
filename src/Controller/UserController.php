<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/list", name="users_list")
     */
    public function usersListAction(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->setName('users_table')
            ->add('name', TextColumn::class, ['field' => 'u.name', 'searchable' => true])
            ->add('surname', TextColumn::class, ['field' => 'u.surname', 'searchable' => true])
            ->add('email', TextColumn::class, ['field' => 'u.email', 'searchable' => true])
            ->add('student', TwigColumn::class, ['template' => 'school/user/_partials/student_field.html.twig', 'searchable' => false])
            ->add('createdAt', DateTimeColumn::class, ['format' => 'd.m.Y H:i:s'])
            ->add('actions', TwigColumn::class, ['template' => 'school/user/_partials/actions_field.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => User::class,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select('u')
                        ->addSelect('s')
                        ->from(User::class, 'u')
                        ->leftJoin('u.student', 's')
                        ->orderBy('u.surname', 'ASC');
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('school/user/list.html.twig', [
            'datatable' => $table,
        ]);
    }

    /**
     * @Route("/edit/{user}", name="user_edit")
     */
    public function userEditAction(Request $request, User $user, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(UserType::class, $user, ['user' => $user]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            try {
                $manager->flush();
                $this->addFlash('success', 'Zmiany zostały zapisane');

                return $this->redirectToRoute('user_edit', ['user' => $user->getIdUser()]);
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/delete/{user}", name="user_delete")
     */
    public function userDeleteAction(Request $request, User $user, ManagerRegistry $doctrine): Response
    {
        if ($request->get('confirm')) {
            try {
                $doctrine->getManager()->remove($user);
                $doctrine->getManager()->flush();
                $this->addFlash('success', 'Użytkownik został usunięty');

                return $this->redirectToRoute('users_list');
            } catch (\Exception $exc) {
                $this->addFlash('error', $exc->getMessage());
            }
        }

        return $this->render('school/user/delete.html.twig', [
            'user' => $user,
        ]);
    }
}
