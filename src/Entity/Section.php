<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
#[ORM\Table(name: "sections")]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    #[ORM\Column(type: "string", length: 255)]
    private string $content;

    #[ORM\ManyToOne(targetEntity: Section::class, inversedBy: "children")]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Section $parent;

    #[ORM\OneToMany(targetEntity: Section::class, mappedBy: "parent", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $children;

    public function __construct(
        string $title,
        string $content,
        ?Section $parent = null
    ) {
        $this->title = $title;
        $this->content = $content;
        $this->children = new ArrayCollection();
        $this->parent = $parent;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getParent(): ?Section
    {
        return $this->parent;
    }

    public function setParent(?Section $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Collection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Section $child): void
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
    }

    public function removeChild(Section $child): void
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            $child->setParent(null);
        }
    }
}
