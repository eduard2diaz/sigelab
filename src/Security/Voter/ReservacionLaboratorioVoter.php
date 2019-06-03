<?php

namespace App\Security\Voter;

use App\Entity\ReservacionLaboratorio;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class ReservacionLaboratorioVoter extends Voter
{
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager) {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['NEW','EDIT', 'DELETE'])
            && $subject instanceof ReservacionLaboratorio ;
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
            case 'NEW':
                return $this->decisionManager->decide($token, array('ROLE_PROFESOR'));
            break;
            case 'EDIT':
            case 'DELETE':
                $hoy = new \DateTime('now');
                return $this->decisionManager->decide($token, array('ROLE_JEFETECNICO')) || ($this->decisionManager->decide($token, array('ROLE_PROFESOR')) && $subject->getUsuario()->getId() == $token->getUser()->getId() && $subject->getFechainicio()> $hoy) ;
            break;
        }

        return false;
    }
}
