<?php declare(strict_types = 1);

namespace AppBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use \LogicException;

/**
 * Class UserGetter
 * @package AppBundle\Service
 */
class UserGetter
{
    /**
     * @param TokenStorageInterface $tokenStorage
     *
     * @return mixed
     */
    public static function getUserFromToken(TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();
        if (!$user) {
            throw new LogicException(
                'The user is not authenticated!!!'
            );
        }
        return $user;
    }
}