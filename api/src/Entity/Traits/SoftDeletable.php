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

/**
 * Trait SoftDeletable
 * @package App\Entity\Traits
 */
trait SoftDeletable
{
    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    protected DateTime $deletedAt;

    public function getDeletedAt(): DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function updateSoftDeletableTimestamps(): void
    {
        // Create a datetime with microseconds
        $dateTime = DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $this->deletedAt = $dateTime;
    }
}
