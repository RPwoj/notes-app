<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserPatchDto
{
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    public ?string $email;

    #[Assert\Length(
        min: 4,
        max: 10,
    )]
    public ?string $password;
    
    #[Assert\Length(
        min: 4,
        max: 10,
    )]
    public ?string $passwordRepeated;
}