<?php

namespace GIL\QueazyBundle\Controller\User;

use GIL\QueazyBundle\Entity\Utilisateur;
use GIL\QueazyBundle\Entity\Quiz;
use GIL\QueazyBundle\Form\AddQuizType;
use GIL\QueazyBundle\Form\QuizType;
use GIL\QueazyBundle\Entity\Question;
use GIL\QueazyBundle\Form\AddQuestionType;
use GIL\QueazyBundle\Entity\Reponse;
use GIL\QueazyBundle\Form\AddReponseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddQuizController extends Controller
{
    public function addQuizAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $quiz = new Quiz();
        $quiz->setUtilisateur($user);

        $question = new Question();
        $question->setQuiz($quiz);
        $form = $this->createForm(new QuizType(), $question);

        $reponse1 = new Reponse();
        $reponse2 = new Reponse();
        $reponse3 = new Reponse();
        $reponse4 = new Reponse();

        $form_r1 = $this->get('form.factory')->createNamedBuilder('form_add_reponse_1',new AddReponseType(), $reponse1)
            ->getForm();

        $form_r2 = $this->get('form.factory')->createNamedBuilder('form_add_reponse_2',new AddReponseType(), $reponse2)
            ->getForm();

        $form_r3 = $this->get('form.factory')->createNamedBuilder('form_add_reponse_3',new AddReponseType(), $reponse3)
            ->getForm();

        $form_r4 = $this->get('form.factory')->createNamedBuilder('form_add_reponse_4',new AddReponseType(), $reponse4)
            ->getForm();

        $form->handleRequest($request);
        $form_r1->handleRequest($request);
        $form_r2->handleRequest($request);
        $form_r3->handleRequest($request);
        $form_r4->handleRequest($request);

        if('POST' === $request->getMethod() && $form->isValid()) {
            $nbReponse = 0;
            if ($form_r1->isValid()) {
                $reponse1->setQuestion($question);
                $em->persist($reponse1);
                $nbReponse++;
            }

            if ($form_r2->isValid()) {
                $reponse2->setQuestion($question);
                $em->persist($reponse2);
                $nbReponse++;
            }

            if ($form_r3->isValid()) {
                $reponse3->setQuestion($question);
                $em->persist($reponse3);
                $nbReponse++;
            }

            if ($form_r4->isValid()) {
                $reponse4->setQuestion($question);
                $em->persist($reponse4);
                $nbReponse++;
            }

            if ($nbReponse++ >= 2) {
                $em->persist($question);
                $em->persist($quiz);

                $em->flush();

                $listQuestion = $em->getRepository('GILQueazyBundle:Question')->findBy(
                    array('quiz' => $quiz)
                );

                return $this->render('GILQueazyBundle:User:addQuiz.html.twig', array(
                    'quiz' => $quiz,
                    'user' => $user,
                    'listQuestion' => $listQuestion
                ));
            }
        }

        return $this->render('GILQueazyBundle:User:add.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
            'form1' => $form_r1->createView(),
            'form2' => $form_r2->createView(),
            'form3' => $form_r3->createView(),
            'form4' => $form_r4->createView(),
        ));
    }
    public function addAction($quiz_id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $quiz = $em->getRepository('GILQueazyBundle:Quiz')->find($quiz_id);

        $listQuestion = $em->getRepository('GILQueazyBundle:Question')->findBy(
            array('quiz' => $quiz)
        );

        return $this->render('GILQueazyBundle:User:addQuiz.html.twig', array(
            'quiz' => $quiz,
            'user' => $user,
            'listQuestion' => $listQuestion,
        ));
    }

    public function addAnswerAction($quiz_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $quiz = $em->getRepository('GILQueazyBundle:Quiz')->find($quiz_id);

        $question = new Question();
        $question->setQuiz($quiz);

        $reponse1 = new Reponse();
        $reponse2 = new Reponse();
        $reponse3 = new Reponse();
        $reponse4 = new Reponse();
        
        // Formulaire
        $form_q = $this->createForm(new AddQuestionType(), $question);

        $form_r1 = $this->get('form.factory')->createNamedBuilder('form_add_reponse_1',new AddReponseType(), $reponse1)
            ->getForm();

        $form_r2 = $this->get('form.factory')->createNamedBuilder('form_add_reponse_2',new AddReponseType(), $reponse2)
            ->getForm();

        $form_r3 = $this->get('form.factory')->createNamedBuilder('form_add_reponse_3',new AddReponseType(), $reponse3)
            ->getForm();

        $form_r4 = $this->get('form.factory')->createNamedBuilder('form_add_reponse_4',new AddReponseType(), $reponse4)
            ->getForm();
        
        // On fait le lien Requête <-> Formulaire
        $form_q->handleRequest($request);
        $form_r1->handleRequest($request);
        $form_r2->handleRequest($request);
        $form_r3->handleRequest($request);
        $form_r4->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        if('POST' === $request->getMethod() && $form_q->isValid()) {
            $nbReponse = 0;
            if ($form_r1->isValid()) {
                $reponse1->setQuestion($question);
                $em->persist($reponse1);
                $nbReponse++;
            }

            if ($form_r2->isValid()) {
                $reponse2->setQuestion($question);
                $em->persist($reponse2);
                $nbReponse++;
            }

            if ($form_r3->isValid()) {
                $reponse3->setQuestion($question);
                $em->persist($reponse3);
                $nbReponse++;
            }

            if ($form_r4->isValid()) {
                $reponse4->setQuestion($question);
                $em->persist($reponse4);
                $nbReponse++;
            }

            if ($nbReponse++ >= 2) {
                $em->persist($question);
                $em->persist($quiz);

                $em->flush();

                $listQuestion = $em->getRepository('GILQueazyBundle:Question')->findBy(
                    array('quiz' => $quiz)
                );
                return $this->render('GILQueazyBundle:User:addQuiz.html.twig', array(
                    'quiz' => $quiz,
                    'user' => $user,
                    'listQuestion' => $listQuestion
                ));
            }
        }
        
        return $this->render('GILQueazyBundle:User:addQuestion.html.twig', array(
            'form' => $form_q->createView(),
            'form1' => $form_r1->createView(),
            'form2' => $form_r2->createView(),
            'form3' => $form_r3->createView(),
            'form4' => $form_r4->createView(),
            'user' => $user,
            'quiz' => $quiz
        ));
    }
}
