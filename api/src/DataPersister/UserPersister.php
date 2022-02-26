<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Person;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPersister implements ContextAwareDataPersisterInterface
{
    private ?Request $request;

    public function __construct(private UserPasswordHasherInterface $encoder, private EntityManagerInterface $em, private RequestStack $requestStack)
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof user;
    }

    public function persist($data, array $context = []): user
    {
        if ($data instanceof user) {
            $method = isset($context['collection_operation_name']) ? 'POST' : null;
            if ($method === 'POST') {
                $hash = $this->encoder->hashPassword($data, $data->getPassword());
                $data->setPassword($hash);
                $person = new Person();
                $person->setFirstName(json_decode($this->request->getContent(), null, 512, JSON_THROW_ON_ERROR)->firstName);
                $person->setLastName(json_decode($this->request->getContent(), null, 512, JSON_THROW_ON_ERROR)->lastName);
                $this->em->persist($person);
                $person->setUser($data);
                $this->em->persist($person);
            }
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }

    public function remove($data, array $context = [])
    {
        if ($data instanceof user && $context['item_operation_name'] === 'delete') {
            $this->em->remove($data);
            $this->em->flush();
        }
    }
}
