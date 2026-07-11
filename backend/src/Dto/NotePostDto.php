<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class NotePostDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 60)]
    public $title;
    public $content;
}