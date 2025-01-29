<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\TestQuestion;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/front/course")
 */
class FrontCourseController extends AbstractController
{
    /**
     * @Route("/dashboard/{course}", name="front_course")
     */
    public function coursesAction(Course $course): Response
    {
        return $this->render('front/dashboard.html.twig', [
            'course' => $course,
        ]);
    }

    /**
     * @Route("/test/{course}", name="front_test")
     */
    public function testAction(Course $course, ManagerRegistry $doctrine): Response
    {
        $test = $doctrine->getManager()->getRepository(TestQuestion::class)->findBy(['course' => $course]);

        return $this->render('front/test.html.twig', [
            'course' => $course,
            'test' => $test,
        ]);
    }
}