<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\Role;
use App\Serializer\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface         $serializer,
        private readonly EntityManagerInterface      $entityManager,
        private readonly ValidatorInterface          $validator,
        private readonly UserPasswordHasherInterface $hasher,
    )
    {
    }

    #[Route('/v1/api/users', name: 'api_users_store', methods: ['PUT'])]
    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $this->validate($user);

        $user
            ->setPassword($this->hasher->hashPassword($user, $user->getPass()))
            ->setRoles(
            [str_contains(strtolower($user->getLogin()), 'admin') ? Role::ADMIN->value : Role::USER->value]
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user, context: ['groups' => [Group::VISIBLE]]);
    }

    #[Route('/v1/api/users', name: 'api_users_update', methods: ['POST'])]
    public function update(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $this->validate($user);

        $entity = $this->entityManager->find(User::class, $user->getId());
        $entity
            ->setLogin($user->getLogin())
            ->setPhone($user->getPhone())
            ->setPass($user->getPass())
            ->setPassword($this->hasher->hashPassword($user, $user->getPass()));

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->json($entity, context: ['groups' => [Group::VISIBLE]]);
    }

    #[Route('/v1/api/users/{id}', name: 'api_users_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        if (!$entity = $this->entityManager->find(User::class, $id)) {
            throw new NotFoundHttpException('Entity not found', code: JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($entity, context: ['groups' => [Group::VISIBLE]]);
    }

    #[Route('/v1/api/users/{id}', name: 'api_users_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $entity = $this->entityManager->find(User::class, $id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return $this->json([], status: JsonResponse::HTTP_NO_CONTENT);
    }

    protected function validate($value): void
    {
        if (($errors = $this->validator->validate($value))->count()) {
            throw new BadRequestHttpException(
                sprintf('%s => %s', $errors->get(0)->getPropertyPath(), $errors->get(0)->getMessage())
            );
        }
    }
}
