<?php

namespace Test\TwitterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Test\TwitterBundle\Entity\User;
use Test\TwitterBundle\Form\RegisterType;
use Test\TwitterBundle\Form\LoginType;


class UserController extends Controller
{
    public function registerAction()
    {
        $user = new User();
        $form = $this->createForm(new RegisterType(),$user);

        $request = $this->get('request');
        $form->handleRequest($request);

        if($request->getMethod() == 'POST'){
            if($form->isValid()){

                $username = $form->get('username')->getData();
                $plainPassword = $form->get('password')->getData();
                $email = $form->get('email')->getData();

                //password encryption
                $encoder = $this->container->get('security.password_encoder');
                $password = $encoder->encodePassword($user, $plainPassword);

                $user->setUsername($username);
                $user->setPassword($password);
                $user->setEmail($email);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->render('TestTwitterBundle:User:register.html.twig',
                    array('form' => $form->createView()));
            }

        }

        return $this->render('TestTwitterBundle:User:register.html.twig', array('form' => $form->createView()));
    }

    public function allUsersAction(){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        //get all users
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('TestTwitterBundle:User')
            ->findAll();

        return $this->render('TestTwitterBundle:User:allUsers.html.twig', array( 'users' => $users));

    }
}
