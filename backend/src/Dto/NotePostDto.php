<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class NotePostDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(
            min: 2,
            max: 60,
        )]
        public $title,
    ) {
    }
}