<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserPostDto {
    #[Assert\Length(
        min: 4,
        max: 10,
    ), 
    Assert\NotBlank(
        message: 'Password field cannot be empty.',
    )]
    public $password;

    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    ), 
    Assert\NotBlank(
        message: 'Email field cannot be empty.',
    )]
    public $email;
}