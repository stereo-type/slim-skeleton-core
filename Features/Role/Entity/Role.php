<?php

namespace App\Core\Features\Role\Entity;

use App\Core\Entity\Traits\HasTimestamps;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table('roles')]
#[ORM\HasLifecycleCallbacks]
class Role
{
    use HasTimestamps;

    public const ADMIN = 'admin';

    public const MANAGER = 'manager';

    public const CUSTOMER = 'customer';

    #[ORM\Id, ORM\Column(options: ['unsigned' => true]), ORM\GeneratedValue]
    private int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[ORM\Column(length: '64', unique: true)]
    private string $name;

    #[ORM\Column(length: '255', nullable: true, options: ['default' => null])]
    private ?string $fullname = null;

    #[ORM\Column(length: '64', nullable: true, options: ['default' => null])]
    private ?string $archetype = null;

    #[ORM\Column(length: '255', nullable: true, options: ['default' => null])]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: RoleAssignment::class, cascade: ["persist", "remove"])]
    private Collection $assignments;

    #[ORM\OneToMany(mappedBy: 'permission', targetEntity: RolePermission::class, cascade: ["persist", "remove"])]
    private Collection $permissions;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getFullname(): string
    {
        return $this->fullname;
    }

    public function setFullname(?string $fullname): self
    {
        $this->fullname = $fullname;
        return $this;
    }

    public function getArchetype(): string
    {
        return $this->archetype;
    }

    public function setArchetype(?string $archetype): self
    {
        $this->archetype = $archetype;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function setAssignments(Collection $assignments): self
    {
        $this->assignments = $assignments;
        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function setPermissions(Collection $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }


}
