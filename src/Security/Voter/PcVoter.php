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
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
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
                if ($this->decisionManager->decide($token, array('ROLE_TECNICO')) || $token->getUser()->getFacultad()->getIdlaboratorio()->contains($subject->getLaboratorio()))
                    return true;
                break;
            case 'NEW':
            case 'DELETE':
                if ($this->decisionManager->decide($token, array('ROLE_JEFETECNICO')) || $token->getUser()->getFacultad()->getIdlaboratorio()->contains($subject->getLaboratorio()))
                    return true;
                break;
        }

        return false;
    }
}
