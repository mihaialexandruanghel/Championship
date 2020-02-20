<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categoryName;

    /**
     * @ORM\Column(type="integer")
     */
    private $competitionResult;

    /**
     * @ORM\Column(type="integer")
     */
    private $categoryAge;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Results", mappedBy="category")
     */
    private $results;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="categories")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Competition", mappedBy="category")
     */
    private $competitions;

    public function __toString()
    {
        return $this->categoryName;
    }


    public function __construct()
    {
        $this->results = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->competitions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): self
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    public function getCompetitionResult(): ?int
    {
        return $this->competitionResult;
    }

    public function setCompetitionResult(int $competitionResult): self
    {
        $this->competitionResult = $competitionResult;

        return $this;
    }

    public function getCategoryAge(): ?int
    {
        return $this->categoryAge;
    }

    public function setCategoryAge(int $categoryAge): self
    {
        $this->categoryAge = $categoryAge;

        return $this;
    }

    /**
     * @return Collection|Results[]
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function addResult(Results $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
            $result->setCategory($this);
        }

        return $this;
    }

    public function removeResult(Results $result): self
    {
        if ($this->results->contains($result)) {
            $this->results->removeElement($result);
            // set the owning side to null (unless already changed)
            if ($result->getCategory() === $this) {
                $result->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * @return Collection|Competition[]
     */
    public function getCompetitions(): Collection
    {
        return $this->competitions;
    }

    public function addCompetition(Competition $competition): self
    {
        if (!$this->competitions->contains($competition)) {
            $this->competitions[] = $competition;
            $competition->addCategory($this);
        }

        return $this;
    }

    public function removeCompetition(Competition $competition): self
    {
        if ($this->competitions->contains($competition)) {
            $this->competitions->removeElement($competition);
            $competition->removeCategory($this);
        }

        return $this;
    }
}
