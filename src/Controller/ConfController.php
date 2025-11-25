<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Form\ConferenceType;
use App\Repository\ConferenceRepository;
use App\Repository\PathologieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/conf')]
final class ConfController extends AbstractController
{
    #[Route(name: 'app_conf_index', methods: ['GET'])]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // redirect to login route (typical name created by maker: app_login)
            return $this->redirectToRoute('app_login');
        }

        // Normalize roles: remove ROLE_ prefix and lowercase
        $roles = array_map(static function ($r) {
            return strtolower(preg_replace('/^ROLE_/', '', (string) $r));
        }, $user->getRoles());

        $isAdministratif = in_array('administratif', $roles, true)
            || in_array('admin', $roles, true)
            || in_array('administrateur', $roles, true)
            || in_array('superadmin', $roles, true);

        $isMedecin = in_array('medecin', $roles, true) || in_array('médecin', $roles, true) || in_array('doctor', $roles, true);

        if ($isAdministratif) {
            $confs = $conferenceRepository->findAll();
        } elseif ($isMedecin) {
            $confs = $conferenceRepository->findBy(['medecin' => $user]);
        } else {
            $confs = [];
        }

        return $this->render('conf/index.html.twig', [
            'conferences' => $confs,
        ]);
    }

    #[Route('/by-pathologie', name: 'app_conf_by_pathologie', methods: ['GET'])]
    public function byPathologie(PathologieRepository $pathologieRepository, ConferenceRepository $conferenceRepository, Request $request): Response
    {
        $pathologies = $pathologieRepository->findAll();

        $selectedId = $request->query->getInt('patho');
        $conferences = [];

        if ($selectedId) {
            $conferences = $conferenceRepository->findBy(['pathologie' => $selectedId]);
        }

        return $this->render('conf/by_pathologie.html.twig', [
            'pathologies' => $pathologies,
            'conferences' => $conferences,
            'selected' => $selectedId,
            
        ]);
    }

    #[Route('/new', name: 'app_conf_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $conference = new Conference();
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // set the currently authenticated user as the medecin for this conference
        $conference->setMedecin($user);
        $form = $this->createForm(ConferenceType::class, $conference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($conference);
            $entityManager->flush();

            return $this->redirectToRoute('app_conf_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('conf/new.html.twig', [
            'conference' => $conference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_conf_show', methods: ['GET'])]
    public function show(Conference $conference): Response
    {
        return $this->render('conf/show.html.twig', [
            'conference' => $conference,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_conf_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Conference $conference, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConferenceType::class, $conference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_conf_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('conf/edit.html.twig', [
            'conference' => $conference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_conf_delete', methods: ['POST'])]
    public function delete(Request $request, Conference $conference, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$conference->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($conference);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_conf_index', [], Response::HTTP_SEE_OTHER);
    }
}
