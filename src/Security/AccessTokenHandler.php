<?php
namespace App\Security;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface {
    public function __construct() {}

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken) : UserBadge {
        // $token = $this->accessTokenRepository->findOneByToken($accessToken);

        if (!$accessToken) {
            throw new BadCredentialsException();
        }

        if (!$accessToken->isValid()) {
            throw new CustomUserMessageAuthenticationException('Token expired');
        }

        return new UserBadge($accessToken->getOwnedBy()->getUserIdentifier());
    }
}