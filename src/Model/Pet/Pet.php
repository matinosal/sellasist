<?php

namespace App\Model\Pet;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

use App\Model\Pet\Enum\Status;

class Pet
{
    public function __construct(
        #[Assert\PositiveOrZero]
        private int $id,
        #[Assert\Length(min: 2, max: 3)]
        private string $name = "",
        #[Assert\Valid]
        private ?Category $category = null,
        private array $photoUrls = [],
         #[Assert\Valid]
        private array $tags = [],
        #[Assert\All([
            new Assert\Url(message: "The photo URL '{{ value }}' is not a valid URL"),
        ])]
        private ?Status $status = null
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhotoUrls(): array
    {
        return $this->photoUrls;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        $this->tags[] = $tag;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }
}