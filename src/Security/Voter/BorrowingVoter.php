<?php

namespace App\Security\Voter;

use App\Entity\Borrowing;
use App\Entity\Cotisation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class BorrowingVoter extends Voter
{
    const STARTED_BORROWING = 'STARTED_BORROWING';

    private $decisionManager;
    private $security;

    public function __construct(AccessDecisionManagerInterface $decisionManager, Security $security)
    {
        $this->decisionManager = $decisionManager;
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // Si l'attribut n'est pas supporté, on l'ignore.
        if (!in_array($attribute, array(self::STARTED_BORROWING)))
            return false;

        //On vérifie que cela correspond bien à un emprunt de la base de donnée.
        if (!$subject instanceof Borrowing)
            return false;

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        $borrowing = $subject;

        // L'utilisateur doit être connecté (non anonyme), sinon accès interdit.
        if (!$user instanceof User)
            return false;

        //L'utilisateur est bien celui qui a prêté l'équipement.
        return ($user->getId() === $borrowing->getLendBy()->getId())or($this->security->isGranted('ROLE_ADMIN'));


    }
}