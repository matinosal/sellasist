<?php

namespace App\Model\Pet;

#[OA\Schema(
    schema: "Tag"
)]
class Tag
{
    private int $id;
    private string $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}