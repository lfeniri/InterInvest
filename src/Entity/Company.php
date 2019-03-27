<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * 
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     */
    private $sirenNumber;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     */
    private $cityOfRegistration;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="datetime")
     */
    private $registrationDate;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="float")
     */
    private $capital;

    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\LegalForm", inversedBy="companies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $legalForm;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Address", mappedBy="company",cascade={"persist"}, orphanRemoval=true)
     */
    private $addresses;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSirenNumber(): ?string
    {
        return $this->sirenNumber;
    }

    public function setSirenNumber(string $sirenNumber): self
    {
        $this->sirenNumber = $sirenNumber;

        return $this;
    }

    public function getCityOfRegistration(): ?string
    {
        return $this->cityOfRegistration;
    }

    public function setCityOfRegistration(string $cityOfRegistration): self
    {
        $this->cityOfRegistration = $cityOfRegistration;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getCapital(): ?float
    {
        return $this->capital;
    }

    public function setCapital(float $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    public function getLegalForm(): ?LegalForm
    {
        return $this->legalForm;
    }

    public function setLegalForm(?LegalForm $legalForm): self
    {
        $this->legalForm = $legalForm;

        return $this;
    }

    public function setAddresses($addresses): self
    {
        $this->addresses = $addresses;
        return $this;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAllAddresses()
    {
        return $this->addresses;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection
    {
        $liste = new ArrayCollection();
        foreach($this->getAllAddresses() as $address) {
            if(!$address->isDeleted()) {
                $liste->add($address);
            }
        }
        return $liste;
    }


    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setCompany($this);
        }

        return $this;
    }
    

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->contains($address)) {
            $address->setIsDeleted(true);
        }

        return $this;
    }
}
