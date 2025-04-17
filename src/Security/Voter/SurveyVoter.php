<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Survey;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class SurveyVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Survey;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Survey $survey */
        $survey = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($user, $survey);

            case self::VIEW:
                return $this->canView($user, $survey);
        }

        return false;
    }

    private function canEdit(UserInterface $user, Survey $survey): bool
    {
        return $user === $survey->getAuthor() ||
            in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    private function canView(UserInterface $user, Survey $survey): bool
    {
        return $survey->isActive() ||
            $user === $survey->getAuthor() ||
            in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
