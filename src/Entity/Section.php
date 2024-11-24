<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "sections")]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(name: "has_parent", type: "boolean")]
    private bool $hasParent;

    #[ORM\Column(name: "parent_id", type: "integer")]
    private int $parentId;


    public function getId(): int
    {
        return $this->id;
    }

    public function isHasParent(): bool
    {
        return $this->hasParent;
    }

    public function setHasParent(bool $hasParent): void
    {
        $this->hasParent = $hasParent;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function setParentId(int $parentId): void
    {
        $this->parentId = $parentId;
    }
}
