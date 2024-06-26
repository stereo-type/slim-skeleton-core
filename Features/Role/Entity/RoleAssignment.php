<?php

namespace App\Core\Features\Role\Entity;

use App\Core\Features\Role\Role\RoleAssignmentInterface;
use App\Core\Features\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('role_assignments')]
#[ORM\UniqueConstraint(name: 'video_unique_idx', columns: ['user_id', 'role_id'])]
class RoleAssignment implements RoleAssignmentInterface
{
    #[ORM\Id, ORM\Column(options: ['unsigned' => true]), ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    private Role $role;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;
        return $this;
    }
}