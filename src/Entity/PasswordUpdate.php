<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;



class PasswordUpdate
{

    private $id;


    private $OldPassword;
    /**
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit faire au moins 8 caractères")
     */
    private $NewPassword;
    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Vous n'avez pas confirmé correctement votre
      nouveau mot de passe")
     */


    private $ConfirmPassword;



    public function getOldPassword(): ?string
    {
        return $this->OldPassword;
    }

    public function setOldPassword(string $OldPassword): self
    {
        $this->OldPassword = $OldPassword;

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

    public function getNewPassword(): ?string
    {
        return $this->NewPassword;
    }

    public function setNewPassword(string $NewPassword): self
    {
        $this->NewPassword = $NewPassword;

        return $this;
    }
}
