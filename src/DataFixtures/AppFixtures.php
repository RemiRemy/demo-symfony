<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Categorie;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categorie = new Categorie();
        $categorie->setDesignation("café");

        $manager->persist($categorie);

        $article = new Article();
        $article->setDesignation("Café en grain")
            ->setDescription("Du café pas cher")
            ->setPrix("0.30")
            ->setDateCreation(new DateTime())
            ->setCategorie($categorie);


        $manager->persist($article);

        $manager->flush();
    }
}
