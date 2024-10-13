<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity(fields: ["name"])]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["category:list", "category:read", "manga:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    #[Groups(["category:list", "category:read", "manga:read"])]
    private ?string $name = null;

    /**
     * @var Collection<int, Manga>
     */
    #[ORM\ManyToMany(targetEntity: Manga::class, mappedBy: 'categories')]
    #[Groups(["category:read"])]
    private Collection $mangas;

    public function __construct()
    {
        $this->mangas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Manga>
     */
    public function getMangas(): Collection
    {
        return $this->mangas;
    }

    public function addManga(Manga $manga): static
    {
        if (!$this->mangas->contains($manga)) {
            $this->mangas->add($manga);
            $manga->addCategory($this);
        }

        return $this;
    }

    public function removeManga(Manga $manga): static
    {
        if ($this->mangas->removeElement($manga)) {
            $manga->removeCategory($this);
        }

        return $this;
    }
}
