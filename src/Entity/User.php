<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'email indiqué est déjà utilisé"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Merci de préciser un email")
     * @Assert\Email(message="L'email n'est pas au bon format",
     *               checkMX=true)
     *
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Merci de préciser un nom d'utilisateur")
     * @Assert\Length(min="5",
     *     minMessage="Nom d'utilisateur trop court")
     *
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])#",message="Le mot de passe doit contenir au moins une
     majuscule et un chiffre")
     * @Assert\Length(min="8",max="30",
     *     minMessage="Le mot de passe doit avoir minimum 8 caractères")
     * @Assert\NotBlank(message="Merci de préciser un mot de passe")
     * @Assert\EqualTo(propertyPath="confirm_password",
     * message="Les mots de passes doivent être identiques ")
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Merci de confirmer le mot de passe")
     * @Assert\EqualTo(propertyPath="password",
     *     message="Les mots de passes doivent être identiques")
     */
    public $confirm_password;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getRoles()
    {
        $roles = $this ->userRoles->map(function ($role){
            return $role->getTitle();
        })->toArray();

        $roles[] ='ROLE_USER';
       return $roles;
    }


    public function getSalt()
    {

    }


    public function eraseCredentials()
    {

    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
        }

        return $this;
    }
}
