<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 15/05/19
 * Time: 15:10
 */

namespace App\Entity\Traits;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

trait Timestampable
{
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private ?DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private ?DateTime $updatedAt;

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function updateTimestamps(): void
    {
        // Create a datetime with microseconds
        $dateTime = DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $this->setCreatedAt(null);
        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt($dateTime);
        }
        $this->setUpdatedAt($dateTime);
    }
}
