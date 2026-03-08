<?php

namespace App\Model\Pet;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

use App\Model\Pet\Enum\Status;

#[OA\Schema(
    schema: "Pet"
)]
class Pet
{
    #[Assert\PositiveOrZero]
    #[OA\Property(example: 10000)]
    private int $id;

    #[Assert\Length(
        min: 2, 
        max: 255,
        minMessage: "Name must be at least {{ limit }} characters long", 
        maxMessage: "Name cannot be longer than {{ limit }} characters")]
    #[OA\Property(example: "Carmelo")]
    private string $name;

    #[Assert\Valid]
    #[Type(Category::class)]
    private ?Category $category;

    #[Assert\All([
        new Assert\Url(message: "The photo URL '{{ value }}' is not a valid URL"),
    ])]
    #[OA\Property(
        type: "array",
        items: new OA\Items(
            type: "string",
            example: "http://test.com/",
        )
    )]
    private array $photoUrls;

    #[Assert\Valid]
    #[OA\Property(
        type: "array",
        items: new OA\Items(
            ref: new Model(type: Tag::class) 
        )
    )]
    private array $tags;

    #[Type(Status::class)]
    #[OA\Property(example: "available")]
    private ?Status $status;

    public function getId(): int { return $this->id; }
    public function setId(int $id): self { $this->id = $id; return $this; }
    
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getStatus(): ?Status { return $this->status; }
    public function setStatus(?Status $status): self { $this->status = $status; return $this; }

    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): self { $this->category = $category; return $this; }

    public function getPhotoUrls(): array { return $this->photoUrls; }
    public function setPhotoUrls(array $photoUrls): self { $this->photoUrls = $photoUrls; return $this; }

    public function getTags(): array { return $this->tags; }
    public function setTags(array $tags): self { $this->tags = $tags; return $this; }
}