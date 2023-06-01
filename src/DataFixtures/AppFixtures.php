<?php

namespace App\DataFixtures;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Ingredient;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/** EXPLICATION FIXTURE ET FAKER
 * Creer des faux données avec Faker
 * AppFixtures sert juste à effacer les données d'avant et 
 * remplacer par les données générer par faker
 * On utilise une boucle pour générer ici 10 jeux de données
 */

 /** EXPLICATION HASHAGE DES PASSWORDS
  * Bref, le problème c'est que pour éviter d'utiliser des fonctions
  * redondantes dans les controleurs pour hasher le mdp lors de la création 
  * et la mise à jour des utilisateurs, on utilise ce qu'on appelle les entity Listeners 
  * ce sont des fichiers qui vont écouter ce qui se passe au niveau des entités 
  * et ils vont faire des actions au niveau de la persistance, la MAJ etc
  * Pour faire cela, on va aller dans le fichier service.yaml
  * Editer le fichier selon les commentaires faites dedans
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
        
            //Users
        for ($i = 0; $i < 10; $i++){
            $user = new User();
            $user->setFullname($this->faker->name())
                ->setPseudo(mt_rand(0,1)===1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');
                
            $manager->persist($user);
        }
        $manager->flush();
    }


}
