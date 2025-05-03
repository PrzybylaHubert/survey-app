<?php

declare(strict_types=1);

namespace App\Security;

use phpseclib3\Crypt\RSA;
use phpseclib3\File\X509;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TpayWebhookAuthenticator extends AbstractAuthenticator
{
    private const TPAY_URL = 'https://secure.tpay.com';
    private const TPAY_URL_SANDBOX = 'https://secure.sandbox.tpay.com';

    public function __construct()
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/api/await-notification';
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $jws = $request->headers->get('X-JWS-Signature');
        if (is_null($jws)) {
            throw new AuthenticationException('Missing JWS header');
        }

        $jwsParts = explode('.', $jws);
        if (count($jwsParts) !== 3) {
            throw new AuthenticationException('Invalid JWS structure');
        }

        [$encodedHeaders, $encodedPayload, $encodedSignature] = $jwsParts;

        $headersJson = base64_decode(strtr($encodedHeaders, '-_', '+/'));
        $headers = json_decode($headersJson, true);

        $x5u = $headers['x5u'] ?? null;
        if (is_null($x5u)) {
            throw new AuthenticationException('Missing x5u header');
        }
        
        $environment = $this->getTpayEnvironment($x5u);

        try {
            $certificate = file_get_contents($x5u);
            $trusted = file_get_contents($environment . '/x509/tpay-jws-root.pem');
        } catch (\ErrorException) {
            throw new AuthenticationException('Cannot fetch certificate');
        }

        $x509 = new X509();
        $x509->loadX509($certificate);
        $x509->loadCA($trusted);
        if (!$x509->validateSignature()) {
            throw new AuthenticationException('Certificate not signed by Tpay CA');
        }

        $body = $request->getContent();
        $encodedBody = str_replace('=', '', strtr(base64_encode($body), '+/', '-_'));
        $decodedSignature = base64_decode(strtr($encodedSignature, '-_', '+/'));

        $publicKey = $x509->getPublicKey()
            ->withHash('sha256')
            ->withPadding(RSA::SIGNATURE_PKCS1);

        $valid = $publicKey->verify($encodedHeaders . '.' . $encodedBody, $decodedSignature);
        if (!$valid) {
            throw new AuthenticationException('Invalid JWS signature');
        }

        return new SelfValidatingPassport(new UserBadge($request->request->get('tr_email')));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response('Forbidden', Response::HTTP_FORBIDDEN);
    }

    private function getTpayEnvironment(string $x5u): string
    {
        if (str_starts_with($x5u, self::TPAY_URL_SANDBOX)) {
            return self::TPAY_URL_SANDBOX;
        }

        if (str_starts_with($x5u, self::TPAY_URL)) {
            return self::TPAY_URL;
        }

        throw new AuthenticationException('Unknown Tpay environment');
    }
}
