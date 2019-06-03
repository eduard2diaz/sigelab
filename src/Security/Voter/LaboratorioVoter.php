<?php

namespace App\Security\Voter;

use App\Entity\Laboratorio;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class LaboratorioVoter extends Voter
{
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager) {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['VIEW','EDIT'])
            && $subject instanceof Laboratorio ;
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
                return ($this->decisionManager->decide($token, array('ROLE_JEFETECNICOINSTITUCIONAL')) || ($this->decisionManager->decide($token, array('ROLE_TECNICO')) && $token->getUser()->getFacultad()->getIdlaboratorio()->contains($subject)));
            break;
            case 'EDIT':
                return ($this->decisionManager->decide($token, array('ROLE_JEFETECNICOINSTITUCIONAL')) || ($this->decisionManager->decide($token, array('ROLE_JEFETECNICO')) && $token->getUser()->getFacultad()->getIdlaboratorio()->contains($subject)));
            break;
        }

        return false;
    }
}
