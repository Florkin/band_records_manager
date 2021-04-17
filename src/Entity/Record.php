<?php

namespace App\Entity;

use App\Repository\RecordRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RecordRepository::class)
 * @Vich\Uploadable
 */
class Record
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="song_record", fileNameProperty="recordName")
     * @Assert\File(
     *     mimeTypes = {"audio/aac", "audio/mpeg"},
     *     mimeTypesMessage = "Wrong file type (aac, mp3)"
     * )
     * @var File|null
     */
    private $recordFile;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $recordName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $archivedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Song::class, inversedBy="records")
     */
    private $song;

    public function __toString()
    {
        return $this->recordName;
    }

    /**
     * Record constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $recordFile
     */
    public function setRecordFile(?File $recordFile = null): void
    {
        $this->recordFile = $recordFile;

        if (null !== $recordFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getRecordFile(): ?File
    {
        return $this->recordFile;
    }

    public function setRecordName(?string $recordName): void
    {
        $this->recordName = $recordName;
    }

    public function getRecordName(): ?string
    {
        return $this->recordName;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getArchivedAt(): ?\DateTimeInterface
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?\DateTimeInterface $archivedAt): self
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }

    public function getSong(): ?Song
    {
        return $this->song;
    }

    public function setSong(?Song $song): self
    {
        $this->song = $song;

        return $this;
    }
}
