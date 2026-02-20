<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Publication;
use App\Entity\Tag;
use App\Entity\Commentaire;
use App\Entity\Reaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création des tags
        $tags = [];
        foreach (['PHP', 'Symfony', 'JavaScript', 'Docker', 'React'] as $tagName) {
            $tag = new Tag();
            $tag->setName($tagName);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        // Création des users
        $users = [];

        $admin = new User();
        $admin->setEmail('admin@test.com')
            ->setFirstName('Admin')
            ->setLastName('Admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($admin, 'password'))
            ->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($admin);
        $users[] = $admin;

        $banned = new User();
        $banned->setEmail('banned@test.com')
            ->setFirstName('Banned')
            ->setLastName('User')
            ->setRoles(['ROLE_BANNED'])
            ->setPassword($this->hasher->hashPassword($banned, 'password'))
            ->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($banned);
        $users[] = $banned;

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->hasher->hashPassword($user, 'password'))
                ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($user);
            $users[] = $user;
        }

        // Création des publications
        $publications = [];
        for ($i = 0; $i < 20; $i++) {
            $publication = new Publication();
            $publication->setTitle($faker->sentence())
                ->setContent($faker->paragraphs(3, true))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setAuthor($faker->randomElement($users));

            // Ajout de tags aléatoires
            $randomTags = $faker->randomElements($tags, rand(1, 3));
            foreach ($randomTags as $tag) {
                $publication->addTag($tag);
            }

            $manager->persist($publication);
            $publications[] = $publication;
        }

        // Création des commentaires
        for ($i = 0; $i < 50; $i++) {
            $commentaire = new Commentaire();
            $commentaire->setContent($faker->sentence())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setAuthor($faker->randomElement($users))
                ->setPublication($faker->randomElement($publications));
            $manager->persist($commentaire);
        }

        // Création des réactions
        for ($i = 0; $i < 50; $i++) {
            $reaction = new Reaction();
            $reaction->setType($faker->randomElement(['like', 'dislike']))
                ->setAuthor($faker->randomElement($users))
                ->setPublication($faker->randomElement($publications));
            $manager->persist($reaction);
        }

        $manager->flush();
    }
}