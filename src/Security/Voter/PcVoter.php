<?php

namespace App\Security\Voter;

use App\Entity\Pc;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class PcVoter extends Voter
{
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT', 'DELETE', 'NEW', 'VIEW'])
            && $subject instanceof Pc;
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
            case 'VIEW':
            case 'EDIT':
                return ($this->decisionManager->decide($token, array('ROLE_JEFETECNICOINSTITUCIONAL')) || ($this->decisionManager->decide($token, array('ROLE_TECNICO')) && $token->getUser()->getFacultad()->getIdlaboratorio()->contains($subject->getLaboratorio())));
            break;
            case 'NEW':
            case 'DELETE':
                return ($this->decisionManager->decide($token, array('ROLE_JEFETECNICOINSTITUCIONAL')) || ($this->decisionManager->decide($token, array('ROLE_JEFETECNICO')) || $token->getUser()->getFacultad()->getIdlaboratorio()->contains($subject->getLaboratorio())));
            break;
        }

        return false;
    }
}
