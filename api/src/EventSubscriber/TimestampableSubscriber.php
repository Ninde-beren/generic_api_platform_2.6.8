<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17/05/19
 * Time: 13:25
 */

namespace App\EventSubscriber;

use App\Entity\Traits\SoftDeletable;
use App\Entity\Traits\Timestampable;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\ORMException;

class TimestampableSubscriber implements EventSubscriber
{
    private string $dbFieldType = 'datetime';
    private string $timestampableTrait = Timestampable::class;
    private string $softdeleteTrait = SoftDeletable::class;

    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
            Events::onFlush,
        ];
    }

    /**
     * @throws MappingException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        if (null === $classMetadata->reflClass) {
            return;
        }
        if ($this->isTimestampable($classMetadata)) {
            $classMetadata->addLifecycleCallback('updateTimestamps', Events::prePersist);
            $classMetadata->addLifecycleCallback('updateTimestamps', Events::preUpdate);

            foreach (['createdAt', 'updatedAt'] as $field) {
                if (!$classMetadata->hasField($field)) {
                    $classMetadata->mapField(
                        [
                            'fieldName' => $field,
                            'type'      => $this->dbFieldType,
                            'nullable'  => true,
                        ]
                    );
                }
            }
        }
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em  = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $meta = $em->getClassMetadata($entity::class);

            if ($this->isSoftdeleteTable($meta)) {
                $reflProp = $meta->getReflectionProperty('deletedAt');
                $oldValue = $reflProp->getValue($entity);
                $date     = new \DateTime();

                $reflProp->setValue($entity, $date);

                $em->persist($entity);
                $uow->propertyChanged($entity, 'deletedAt', $oldValue, $date);
                $uow->scheduleExtraUpdate(
                    $entity,
                    [
                        'deletedAt' => [$oldValue, $date],
                    ]
                );
            }
        }
    }

    /**
     * Checks if entity is timestampable
     *
     * @param ClassMetadata $classMetadata The metadata
     *
     * @return Boolean
     */
    private function isTimestampable(ClassMetadata $classMetadata)
    {
        return (in_array($this->timestampableTrait, $classMetadata->reflClass->getTraitNames()));
    }

    /**
     * Checks if entity is softdelete
     *
     * @param ClassMetadata $classMetadata The metadata
     *
     * @return Boolean
     */
    private function isSoftdeleteTable(ClassMetadata $classMetadata)
    {
        return (in_array($this->softdeleteTrait, $classMetadata->reflClass->getTraitNames()));
    }
}
