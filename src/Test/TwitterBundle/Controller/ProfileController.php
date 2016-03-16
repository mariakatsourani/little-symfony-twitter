<?php

namespace Test\TwitterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use Test\TwitterBundle\Entity\Status;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\Session\Session;

class ProfileController extends Controller
{
    public function showAction($username)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $repository = $this->getDoctrine()->getRepository('TestTwitterBundle:User');
        $profile = $repository->findOneBy(array('username' => $username));
        if(!$profile){
            throw $this->createNotFoundException('This user does not exist!');
        }else if($this->getUser()->getUsername() == $profile->getUsername()){
            return $this->redirectToRoute('edit');
        }else{
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT r FROM TestTwitterBundle:Relationship r
                    WHERE (r.follower =:current_user AND r.following =:profile)
                    OR (r.follower =:profile AND r.following =:current_user) ');
            $query->setParameters(array(
                'current_user' => $this->getUser()->getId(),
                'profile' => $profile->getId(),
            ));
            $results = $query->getResult();
        }

        if($results){
            foreach($results as $result){
                if($result->getFollower() == $this->getUser()->getId()){
                    $relationship['following'] = true;
                }else if($result->getFollower() == $profile->getId()){
                    $relationship['followed_by'] = true;
                }
            }
        }else{
            $relationship = array();
        }

        return $this->render('TestTwitterBundle:Profile:show.html.twig', array(
            'profile' => $profile,
            'relationship' => $relationship));

    }

    public function editAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $current_user = $this->getUser();

        // create a status
        $status = new Status();

        $form = $this->createFormBuilder($status)
            ->add('content', 'text')
            ->add('save', 'submit', array('label' => 'Post'))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST') {
            if ($form->isValid()) {
                //get entity manager
                $em = $this->getDoctrine()->getManager();

                //insert status
                $status->setContent($form->get('content')->getData());
                $status->setUserId($current_user->getId());

                $em->persist($status);
                $em->flush();
            }
        }

        //get all the statuses for this user
        $em = $this->getDoctrine()->getManager();
        $getStatuses = $em->createQueryBuilder()
            ->select('s.content, s.date')
            ->from('TestTwitterBundle:Status','s')
            ->where('s.userId = ?1')
            ->orderBy('s.date', 'DESC')
            ->setParameter(1, $current_user->getId())
            ->getQuery();
        $statuses = $getStatuses->getResult();


        return $this->render('TestTwitterBundle:Profile:edit.html.twig', array(
            'user' => $current_user,
            'form' => $form->createView(),
            'statuses' => $statuses
        ));

    }

    public function homeAction(){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        //the users' followers
        $getFollowers = $em->createQuery(
            'SELECT u.id, u.username, r.follower FROM TestTwitterBundle:User u
              JOIN TestTwitterBundle:Relationship r
              WITH r.follower = u.id
              WHERE r.following =:current_user');
        $getFollowers->setParameters(array('current_user' => $this->getUser()->getId()));
        $followers = $getFollowers->getResult();

        //people the user is following
        $getFollowing = $em->createQuery(
            'SELECT u.id, u.username, r.following FROM TestTwitterBundle:User u
              JOIN TestTwitterBundle:Relationship r
              WITH r.following = u.id
              WHERE r.follower =:current_user');
        $getFollowing->setParameters(array('current_user' => $this->getUser()->getId()));
        $following = $getFollowing->getResult();

        //status of those the user follows
        $getStatuses = $em->createQuery(
            'SELECT u.username, s.content, s.date FROM TestTwitterBundle:User u
              JOIN TestTwitterBundle:Status s
              WITH u.id = s.userId
              JOIN TestTwitterBundle:Relationship r
              WITH u.id = r.following
              WHERE r.follower =:current_user
              ORDER BY s.date DESC ');
        $getStatuses->setParameters(array(
            'current_user' => $this->getUser()->getId()));
        $statuses = $getStatuses->getResult();

        return $this->render('TestTwitterBundle:Profile:home.html.twig', array(
            'following' => $following,
            'followers' => $followers,
            'statuses' => $statuses ));
    }

}
