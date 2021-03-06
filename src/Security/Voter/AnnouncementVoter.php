<?php

namespace App\Security\Voter;

use App\Entity\Announcement;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AnnouncementVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['show','edit', 'delete'])
            && $subject instanceof Announcement;
    }

    protected function voteOnAttribute($attribute, $announcement, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'show':
                if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_INFLUENCER', $user->getRoles()) || $user == $announcement->getUser()) {
                    return true;
                }
                break;
            case 'edit':
                if (in_array('ROLE_ADMIN', $user->getRoles()) || $user == $announcement->getUser()) {
                    return true;
                }
                break;
            case 'delete':
                if (in_array('ROLE_ADMIN', $user->getRoles()) || $user == $announcement->getUser()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
