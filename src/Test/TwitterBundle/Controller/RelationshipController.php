<?php

namespace Test\TwitterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Test\TwitterBundle\Entity\Relationship;

class RelationshipController extends Controller
{
    public function followAction($username)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        if($username == $this->getUser()->getUsername()){
            return $this->redirectToRoute('edit');
        }else{
            $rel = new Relationship();

            $repository = $this->getDoctrine()->getRepository('TestTwitterBundle:User');
            $profile = $repository->findOneBy(array('username' => $username));

            $em = $this->getDoctrine()->getManager();
            //insert friendship
            $rel->setFollower($this->getUser()->getId());
            $rel->setFollowing($profile->getId());
            $em->persist($rel);
            $em->flush();
        }

        return $this->render('TestTwitterBundle:Relationship:follow.html.twig', array('username' => $username));    }

    public function unfollowAction($username)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        if($username == $this->getUser()->getUsername()){
            return $this->redirectToRoute('edit');
        }else {
            $em = $this->getDoctrine()->getManager();

            $repository = $this->getDoctrine()->getRepository('TestTwitterBundle:User');
            $profile = $repository->findOneBy(['username' => $username]);

            $repository = $this->getDoctrine()->getRepository('TestTwitterBundle:Relationship');
            $rel = $repository->findOneBy(
                array('follower' => $this->getUser()->getId(), 'following' => $profile->getId())
            );
            $em->remove($rel);
            $em->flush();
        }

        return $this->render('TestTwitterBundle:Relationship:unfollow.html.twig', array('username' => $username));    }

}
