<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class NoteUpdateDto
{
    #[Assert\Length(
        min: 2,
        max: 60,
    )]
    public ?string $title;
    public ?string $content;
}