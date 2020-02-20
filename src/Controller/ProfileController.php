<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{

    private $security;
    private $userRepository;

    function __construct(Security $security, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;

    }

    /**
     * @Route("/", name="profile_index", methods={"GET"})
     */
    public function index(ProfileRepository $profileRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'profiles' => $profileRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="profile_new", methods={"GET","POST"})
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param LoggerInterface $logger
     * @return Response
     */
    public function new(Request $request, \Swift_Mailer $mailer, LoggerInterface $logger): Response
    {
        $currentUser = $this->security->getUser();
        $user = $this->userRepository->findOneBy(['username' => $currentUser->getUsername()]);
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setProfile($profile);
            $entityManager->persist($user);
            $entityManager->persist($profile);
            $entityManager->flush();

            $message = new \Swift_Message('Registration email');
            $message->setFrom('tmiss2019@gmail.com');
            $message->setTo($profile->getEmail());
            $message->setBody(
                $this->renderView(
                    'email/mymail.html.twig',
                    ['name' => $profile->getFirstName()]
                ),
                'text/html'
            );

            $mailer->send($message);

            $logger->info('email sent to address');
            $this->addFlash('notice', 'Email sent');

            return $this->redirectToRoute('homepage');
        }



        return $this->render('profile/new.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="profile_show", methods={"GET"})
     */
    public function show(Profile $profile): Response
    {
        return $this->render('profile/show.html.twig', [
            'profile' => $profile,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="profile_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Profile $profile): Response
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="profile_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Profile $profile): Response
    {
        if ($this->isCsrfTokenValid('delete' . $profile->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($profile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('profile_index');
    }
}
