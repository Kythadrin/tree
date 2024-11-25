<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Section;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class SectionsController extends AbstractController
{
    private SectionRepository $sectionRepository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        Environment $twig,
    ) {
        parent::__construct($twig);
        /** @var SectionRepository $sectionRepository */
        $sectionRepository = $this->entityManager->getRepository(Section::class);
        $this->sectionRepository = $sectionRepository;
    }

    public function index(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('/');
        }

        $this->render('sections.html.twig', [
            'pageClass' => 'sections',
            'sections' => $this->sectionRepository->findAll(),
        ]);
    }
}