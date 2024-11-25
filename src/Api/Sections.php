<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Section;
use App\Model\Section as SectionModel;
use App\Model\SectionOutput;
use App\Repository\SectionRepository;
use App\Service\SectionService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class Sections
{
    private SectionRepository $sectionRepository;

    public function __construct(
        private readonly SectionService $sectionService,
        private readonly EntityManagerInterface $entityManager,
    ) {
        /** @var SectionRepository $sectionRepository */
        $sectionRepository = $this->entityManager->getRepository(Section::class);
        $this->sectionRepository = $sectionRepository;
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /** @var string $data */
            $data = file_get_contents('php://input');

            /** @var array{title: string, content: string, parent?: int} $input */
            $input = json_decode($data, true);

            $section = new SectionModel(
                null,
                trim($input['title']),
                trim($input['content']),
                $this->sectionRepository->findOneById((int) $input['parent']),
            );

            if (empty($section->title) || empty($section->content)) {
                http_response_code(400);
                echo json_encode(['message' => 'Title and description are required']);
                return;
            }

            $createdSection = null;
            try {
                $createdSection = $this->sectionService->create($section);

                $this->entityManager->flush();
            } catch (Exception $exception) {
                http_response_code(500);
                echo json_encode([
                    'message' => $exception->getMessage(),
                ]);
                return;
            }

            http_response_code(201);
            echo json_encode(new SectionOutput(
                $createdSection->getId(),
                $createdSection->getTitle(),
                $createdSection->getContent(),
                $createdSection->getParent()->getId(),
            ));
        }
    }

    /** @param string[] $parameters */
    public function edit(array $parameters): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $id = $parameters['id'];
            if ($id === null) {
                http_response_code(400);
                echo json_encode([
                    'message' => 'Id not passed',
                ]);
                return;
            }

            /** @var string $data */
            $data = file_get_contents('php://input');

            /** @var array{title: string, content: string, parent?: int} $input */
            $input = json_decode($data, true);

            $section = new SectionModel(
                (int) $id,
                trim($input['title']),
                trim($input['content']),
                $this->sectionRepository->findOneById((int) $input['parent']),
            );

            try {
                $updatedSection = $this->sectionService->edit((int) $id, $section);
                $this->entityManager->flush();
            } catch (Exception $exception) {
                http_response_code(500);
                echo json_encode([
                    'message' => $exception->getMessage(),
                ]);
                return;
            }

            http_response_code(201);
            echo json_encode(new SectionOutput(
                $updatedSection->getId(),
                $updatedSection->getTitle(),
                $updatedSection->getContent(),
                $updatedSection->getParent()->getId(),
            ));
        }
    }

    /** @param string[] $parameters */
    public function delete(array $parameters): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $id = $parameters['id'];
            if ($id === null) {
                http_response_code(400);
                echo json_encode([
                    'message' => 'Id not passed',
                ]);
                return;
            }

            try {
                $this->sectionService->remove((int) $id);

                $this->entityManager->flush();
            } catch (Exception $exception) {
                http_response_code(500);
                echo json_encode([
                    'message' => $exception->getMessage(),
                ]);
                return;
            }

            http_response_code(204);
        }
    }
}