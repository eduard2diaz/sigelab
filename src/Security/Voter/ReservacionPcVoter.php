<?php

namespace App\Security\Voter;

use App\Entity\ReservacionPc;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class ReservacionPcVoter extends Voter
{
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['EDIT', 'DELETE'])
            && $subject instanceof ReservacionPc;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $token->getUser()->getRoles();
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'EDIT':
            case 'DELETE':
                $hoy = new \DateTime('today');
                $hoy = $hoy->format('d-m-Y');
                $hora = new \DateTime('now');
                $hora = $hora->format('H');
                $fechareservacion = $subject->getFecha()->format('d-m-Y');
                    return $this->decisionManager->decide($token, array('ROLE_ESTUDIANTE')) && $subject->getUsuario()->getId() == $token->getUser()->getId() && ($hoy==$fechareservacion && $hora<20);
                break;
        }

        return false;
    }
}
