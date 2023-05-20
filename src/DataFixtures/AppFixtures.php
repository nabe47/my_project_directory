<?php

namespace App\DataFixtures;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


/**
 * Creer des faux données avec Faker
 * AppFixtures sert juste à effacer les données d'avant et 
 * remplacer par les données générer par faker
 * On utilise une boucle pour générer ici 10 jeux de données
 */
 
class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for($i=0;$i<10;$i++){
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
            ->setPrice(3.0);
            $manager->persist($ingredient);
        }
        

        $manager->flush();
    }
}
