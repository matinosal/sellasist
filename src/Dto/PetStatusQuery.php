<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Model\Pet\Enum\Status;

class PetStatusQuery
{
    #[Assert\NotBlank(message: "Status is required")]
    #[Assert\All([
        new Assert\Choice(
            callback: [Status::class, 'getValues'],
            message: "Status {{ value }} is invalid "
        )
    ])]
    public array $status = [];

    public function __construct(array $status = [])
    {
        $this->status = $status;
    }
}