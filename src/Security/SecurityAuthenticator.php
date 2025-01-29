<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityAuthenticator extends AbstractLoginFormAuthenticator
{

    use TargetPathTrait;

    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate('security_login');
    }

    public function authenticate(Request $request): Passport
    {
        $credentials['username'] = ParameterBagUtils::getRequestParameterValue($request, '_username') ?? '';
        $credentials['password'] = ParameterBagUtils::getRequestParameterValue($request, '_password') ?? '';
        $credentials['csrf_token'] = ParameterBagUtils::getRequestParameterValue($request, '_csrf_token') ?? '';

        $passport = new Passport(
            new UserBadge($credentials['username']),
            new PasswordCredentials($credentials['password']),
            [new RememberMeBadge()]
        );

        $passport->addBadge(new CsrfTokenBadge('authenticate', $credentials['csrf_token']));

        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, $token, $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('index'));
    }
}