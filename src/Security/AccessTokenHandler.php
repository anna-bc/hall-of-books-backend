<?php
namespace App\Security;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface {
    public function __construct(private AccessTokenRepository $accessTokenRepository) {}

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken) : UserBadge {
        $token = $this->accessTokenRepository->findOneByToken($accessToken);

        if (!$token) {
            throw new BadCredentialsException();
        }

        return new UserBadge($token->getOwnedBy()->getUserIdentifier());
    }
}