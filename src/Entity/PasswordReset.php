<?php

namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;


class PasswordReset
{

    private $id;

    /**
     * @Assert\Regex("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]{2,})#",message="Le mot de passe doit contenir au moins une
    majuscule et deux chiffres")
     * @Assert\Length(min="8",max="30",
     *     minMessage="Le mot de passe doit avoir minimum 8 caractères")
     * @Assert\NotBlank(message="Merci de préciser un mot de passe")
     * @Assert\EqualTo(propertyPath="ConfirmPassword",
     * message="Les mots de passes doivent être identiques ")
     */

    private $NewPassword;

    /**
     * @Assert\NotBlank(message="Merci de confirmer le mot de passe")
     * @Assert\EqualTo(propertyPath="NewPassword",
     *     message="Les mots de passes doivent être identiques")
     */
    private $ConfirmPassword;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNewPassword(): ?string
    {
        return $this->NewPassword;
    }

    public function setNewPassword(string $NewPassword): self
    {
        $this->NewPassword = $NewPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->ConfirmPassword;
    }

    public function setConfirmPassword(string $ConfirmPassword): self
    {
        $this->ConfirmPassword = $ConfirmPassword;

        return $this;
    }
}
