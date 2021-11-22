<?php

namespace App\Security;

use App\Repository\TaskRepository;
use App\Service\MailerService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthentificatorAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;

    private MailerService $mailer;

    private TaskRepository $repository;

    private EntityManagerInterface $manager;

    public function __construct(UrlGeneratorInterface $urlGenerator, MailerService $mailer, EntityManagerInterface $manager, TaskRepository $repository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->mailer = $mailer;
        $this->manager = $manager;
        $this->repository = $repository;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $mailUser = $request->request->get('email');
        $userName = explode('@', $mailUser)[0];
        $now = new DateTime();
        $tasks = $this->repository->findAll();

        $msg = "";

        foreach ($tasks as $task) {
            $diffDate = $now->diff($task->getDueAt());

            $parameters = [
                'username' => $userName,
                'task' => $task,
                'msg' => $msg
            ];
            // dump($parameters);

            if ($diffDate->days <= 2 && ($now < $task->getDueAt())) {
                // dd($parameters);
                $msg = "prout";
                $this->mailer->sendEmail("prout", $mailUser, $mailUser, 'emails/alert.html.twig', $parameters);
            } else if ($now > $task->getDueAt()) {
                // dd($parameters);
                $msg = "prout périmé";
                $this->mailer->sendEmail("prout", $mailUser, $mailUser, 'emails/alert.html.twig', $parameters);
            }
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        //For example:
        return new RedirectResponse($this->urlGenerator->generate('task_listing'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
