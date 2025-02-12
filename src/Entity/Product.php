<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 65)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $imageName;
   
    #[Vich\UploadableField(mapping: 'product_images', fileNameProperty: 'imageName')]
    #[Ignore]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $is_featured = null;

    #[ORM\Column]
    private ?int $stockXS = null;

    #[ORM\Column]
    private ?int $stockS = null;

    #[ORM\Column]
    private ?int $stockM = null;

    #[ORM\Column]
    private ?int $stockL = null;

    #[ORM\Column]
    private ?int $stockXL = null;

    public function __construct()
    {
        // Initialiser is_featured à true pour chaque nouveau produit
        $this->is_featured = true;
        $this->updatedAt = new \DateTimeImmutable();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
            $this->updatedAt = new \DateTimeImmutable();
    }
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function isFeatured(): ?bool
    {
        return $this->is_featured;
    }

    public function setIsFeatured(bool $isFeatured): static
    {
        $this->is_featured = $isFeatured;
        return $this;
    }

    public function getStockXS(): ?int
    {
        return $this->stockXS;
    }

    public function setStockXS(int $stockXS): static
    {
        $this->stockXS = $stockXS;
        return $this;
    }

    public function getStockS(): ?int
    {
        return $this->stockS;
    }

    public function setStockS(int $stockS): static
    {
        $this->stockS = $stockS;
        return $this;
    }

    public function getStockM(): ?int
    {
        return $this->stockM;
    }

    public function setStockM(int $stockM): static
    {
        $this->stockM = $stockM;
        return $this;
    }

    public function getStockL(): ?int
    {
        return $this->stockL;
    }

    public function setStockL(int $stockL): static
    {
        $this->stockL = $stockL;
        return $this;
    }

    public function getStockXL(): ?int
    {
        return $this->stockXL;
    }

    public function setStockXL(int $stockXL): static
    {
        $this->stockXL = $stockXL;
        return $this;
    }

    public function isValidSize(string $size): bool
    {
    $method = 'getStock' . strtoupper($size);
    return method_exists($this, $method);
    }

    public function reduceStock(string $size, int $quantity): void
    {
        $stockField = 'stock' . strtoupper($size);
        $currentStock = $this->{'get' . ucfirst($stockField)}();

        if ($quantity > $currentStock) {
            throw new \LogicException('Stock insuffisant.');
        }

        $this->{'set' . ucfirst($stockField)}($currentStock - $quantity);
    }

    public function __serialize(): array
    { 
        return [$this->id]; 
    } 

    public function __unserialize(array $data): void 
    { 
        [$this->id] = $data;
    }
}
