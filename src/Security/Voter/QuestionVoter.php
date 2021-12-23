<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class QuestionVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['edit', 'answer_validate'])
            && $subject instanceof \App\Entity\Question;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'edit':

                // Les MODERATOR (et ADMIN par hiérarchie cf security.yaml) ont le droit aussi
                if ($this->security->isGranted('ROLE_MODERATOR')) {
                    return true;
                }

                // On autorise si le User connecté est l'auteur de la question
                return $user === $subject->getUser();

                break;
                case 'answer_validate':
                    // On autorise si le User connecté est l'auteur de la question
                    if ($user === $subject->getUser()) {
                        return true;
                    }
                break;
        }

        return false;
    }
}
