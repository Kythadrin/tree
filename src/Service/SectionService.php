<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Section;
use App\Model\Section as SectionModel;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;

class SectionService
{
    private SectionRepository $sectionRepository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        /** @var SectionRepository $sectionRepository */
        $sectionRepository = $this->entityManager->getRepository(Section::class);
        $this->sectionRepository = $sectionRepository;
    }

    public function create(SectionModel $sectionData): Section
    {
        $section = new Section(
            $sectionData->title,
            $sectionData->content,
            $sectionData->parent,
        );

        $this->entityManager->persist($section);

        return $section;
    }

    public function remove(int $id): void
    {
        $section = $this->sectionRepository->findOneById($id);

        $this->deleteChildren($section);

        $this->entityManager->remove($section);
    }

    private function deleteChildren(Section $section): void
    {
        foreach ($section->getChildren() as $child) {
            $this->deleteChildren($child);

            $this->entityManager->remove($child);
        }
    }

    public function edit(int $id, SectionModel $sectionData): Section
    {
        $section = $this->sectionRepository->findOneById($id);

        if ($sectionData->title !== null) {
            $section->setTitle($sectionData->title);
        }

        if ($sectionData->content !== null) {
            $section->setContent($sectionData->content);
        }

        if ($sectionData->parent !== null) {
            $section->setParent($sectionData->parent);
        }

        return $section;
    }
}