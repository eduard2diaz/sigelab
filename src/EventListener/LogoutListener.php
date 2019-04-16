<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface
{
    private $em;

    public function __construct( $em)
    {
        $this->em = $em;
    }

    public function logout(Request $request, Response $response, TokenInterface $token){
        $user=$token->getUser();
        $user->setLastlogout(new \DateTime());
        // Persist the data to database.
        $this->em->persist($user);
        $this->em->flush();
    }



}
