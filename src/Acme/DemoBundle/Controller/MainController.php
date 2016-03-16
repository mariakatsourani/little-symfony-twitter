<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\DemoBundle\Entity\Person;
use Acme\DemoBundle\Form\PersonType;
use Symfony\Component\HttpFoundation\Response;


class MainController extends Controller
{
    public function indexAction()
    {
        $person = new Person();
        $form = $this->createForm(new PersonType, $person);

        $request = $this->get('request');
        $form->handleRequest($request);

        if($request->getMethod() == 'POST'){
            if($form->isValid()){
                $email = $form->get('email')->getData();
                $fullname = $form->get('fullname')->getData();

                //set form data in person obj
                $person->setEmail($email);
                $person->setFullname($fullname);

                //store attr of person obj in db
                $em = $this->getDoctrine()->getManager();
                $em->persist($person);
                $em->flush();

                return new Response('Person: ' . $fullname . 'Email: ' . $email . '<br>Account created!');
            }
            return $this->render('AcmeDemoBundle:Main:index.html.twig', array('form' => $form->createView()));
        }

        return $this->render('AcmeDemoBundle:Main:index.html.twig', array('form' => $form->createView()));
    }



    public function otherAction($_route)
    {
        echo $_route;
        echo "other action";
    }


}
