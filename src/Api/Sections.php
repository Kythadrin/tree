<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Section;
use App\Model\Section as SectionModel;
use App\Service\SectionService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class Sections
{
    public function __construct(
        private readonly SectionService $sectionService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /** @var string $data */
            $data = file_get_contents('php://input');

            /** @var array{title: string, content: string, parent: SectionModel} $input */
            $input = json_decode($data, true);

            $section = new SectionModel(
                null,
                trim($input['title']),
                trim($input['content']),
                $input['parent'],
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
            }

            http_response_code(201);
            echo json_encode(new SectionModel(
                $createdSection->getId(),
                $createdSection->getTitle(),
                $createdSection->getContent(),
                $createdSection->getParent(),
            ));
        }
    }

    public function edit(): void
    {

    }

    public function delete(): void
    {

    }
}