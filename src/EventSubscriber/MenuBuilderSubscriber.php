<?php

namespace App\EventSubscriber;

use App\Entity\Course;
use App\Entity\Student;
use App\Entity\StudentCourse;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use KevinPapst\AdminLTEBundle\Event\SidebarMenuEvent;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class MenuBuilderSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private ManagerRegistry $doctrine;

    public function __construct(Security $security, ManagerRegistry $doctrine)
    {
        $this->security = $security;
        $this->doctrine = $doctrine;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SidebarMenuEvent::class => ['onSetupMenu', 100],
        ];
    }

    public function onSetupMenu(SidebarMenuEvent $event)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $event->addItem(new MenuItemModel('main_page_menu', 'Pulpit', 'index', [], 'fas fa-columns'));

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $event->addItem($this->buildAdminMenu());
        }

        if ($user->getStudent()) {
            $event->addItem($this->buildStudentMenu($user->getStudent()));
        }

        $event
            ->addItem(new MenuItemModel('logout', 'Wyloguj', 'security_logout', [], 'fas fa-sign-out-alt'));


        $this->activateByRoute(
            $event->getRequest()->get('_route'),
            $event->getItems()
        );
    }

    private function buildAdminMenu(): MenuItemModel
    {
        $clientMenu = new MenuItemModel('school', 'Administracja', '', [], 'fas fa-school');
        $clientMenu
            ->addChild(new MenuItemModel('courses', 'Kursy', 'courses_list', [], 'fas fa-chalkboard'))
            ->addChild(new MenuItemModel('students', 'Uczniowie', 'students_list', [], 'fas fa-graduation-cap'))
            ->addChild(new MenuItemModel('users', 'Użytkownicy', 'users_list', [], 'fas fa-users'));

        return $clientMenu;
    }

    private function buildStudentMenu(Student $student): MenuItemModel
    {
        $clientMenu = new MenuItemModel('school', 'Kursy', '', [], 'fas fa-chalkboard');
        $courses = $this->doctrine->getManager()->getRepository(StudentCourse::class)->findBy(['student' => $student]);

        /** @var StudentCourse $course */
        foreach($courses as $course) {
            $courseEntity = $course->getCourse();
            $clientMenu->addChild(new MenuItemModel('courses', $courseEntity->getTitle(), 'front_course', ['course' => $courseEntity->getIdCourse()], 'far fa-circle'));
        }

        return $clientMenu;
    }

    protected function activateByRoute(string $route, array $items)
    {
        foreach ($items as $item) {
            if ($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            } elseif ($item->getRoute() == $route) {
                $item->setIsActive(true);
            }
        }
    }
}
