<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', /*Lorsque dans la barre d'adresse on saisit /ingredient après le nom de domaine executer la fonction en dessous*/
    name: 'ingredient.index', /* Le nom de cette route, route ici c'est URL */
    methods:['GET'])] /* Uniquement lorsque saisit dans la barre d'adresse */
    public function index(
    IngredientRepository $repository, /* Pour faire des requêtes sur une table à partir des entités mappées sur cette même table*/
    PaginatorInterface $paginator, /** Pour la pagination des résultats à afficher */
    Request $request /* Pour faire une requête */
    ): Response
    {
        $query = $repository -> findAll(); /** Ici on fait une requête à partir des repository*/

        $ingredient = $paginator->paginate(/** Enregistrer dans la variable $ingredient le résultat de la requête */
            /** et mettre une mise en forme pour s'adapter à la pagination En dessous générer automatiquement */
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/);

               
        return $this->render('pages/ingredient/index.html.twig',/**Atteindre la VUE (la page) lorsque la route est exacte */ [
            'ingredients' => $ingredient, /** Passer les informations dans la variable $ingredient dans cette page*/
        ]);
    }

    /**Pour créer un formulaire saisir tout d'abord dans l'invite de commande php bin/console make:form
     * Puis définir le nom de la classe de cette formulaire par IngredientType
     * Enfin préciser l'entité sur laquelle cette formulaire devra se baser
     * La fonction en dessous a été saisi manuellement
     */
    #[Route('/ingredient/nouveau',name:'ingredient.new',methods:['GET','POST'])]
    public function new(
        Request $request, /** La requête pour le CREATE */
        EntityManagerInterface $manager /* Le manager qui va executer la requête du CREATE*/
        ):Response
    {
        $ingredient = new Ingredient(); /** Créer un nouveau Ingrédient vide pour éviter les erreurs liées à la portée des variables */
        $form = $this->createForm(IngredientType::class, $ingredient);/** Enregister dans la variable $form les informations du formulaire BASES sur l'entité Ingrédient*/

        $form->handleRequest($request); /* Soumettre les données au formulaire */

        if($form->isSubmitted() && $form->isValid()){ /* Si envoie du formulaire, vérifier si les données sont valides */
            $ingredient = $form->getData();/* Recevoir les données du formulaire et stocker dans $ingredient*/
            $manager->persist($ingredient);/* Préparer à evoyer dans la bdd les informations dans $ingredient*/
            $manager->flush();/* Envoyer dans la base de données */
            $this->addFlash( /** Le petit message flash comme dans AUGURE pour dire que c'est enregistrer*/
                'success',/** Le nom du message pour être récupérer dans le twig */
                'Votre ingrédient a été modifié avec succès' /**Le contenu du message */
            );
            return $this->redirectToRoute('ingredient.index'); /**Atteindre la vue qui à pour nom ingredient.index  ici c'est l'affichage des données dans la table*/
            #dd($ingredient); /* Pour afficher les données contenu dans $ingredient, méthode de vérification avant de flush dans la base*/
        }

        return $this->render('pages/ingredient/new.html.twig', /**Atteindre la VUE (la page) lorsque la route est exacte */ [
            'form' => $form->createView() /** Envoyer une vue de formulaire basé sur les information de form/IngredienType */
        ]);
    }

    #[Route('ingredient/edition/{id}', name:'ingredient.edit',methods:['GET','POST'])]
    public function edit(
        Ingredient $ingredient,
        Request $request,
        EntityManagerInterface $manager
    ):Response
    {
        /**
         * Petit Résumer de ce que fait controlleur:
         * 1- Si la lien dans la barre d'adresse correspond à la route executer le controleur
         * 2- Enregistrer dans un form l'ingrédient avec l'id spécifié (explication en bas)
         * 3- Normalement le formulaire est déjà rempli par rapport à l'ingrédient spécifié
         * 4- Soumettre les données du formulaire déjà rempli
         * 5- Vérifier si les données sont ok
         * 6- Recevoir les données du formulaire modifiées
         * 7- Préparer à envoyer le nouvel ingrédient dans la base de données
         * 8- Envoyer dans la base de données
         */
        
        /**Explication de la magie
          *Symfony passe directement l'ingrédient comme paramètre, 
          *Symfony verifie si l'entité ingrédient possède un élément id (comme préciser dans la route)
          *Puis passe directement l'ingrédient (id, name, price, createdAt) dans la vue  
          *Le reste se passe comme dans new ingredient
          */
          $form = $this->createForm(IngredientType::class, $ingredient);
          $form->handleRequest($request); /* Soumettre les données au formulaire */

          if($form->isSubmitted() && $form->isValid()){ /* Si envoie du formulaire, vérifier si les données sont valides */
            $ingredient = $form->getData();/* Recevoir les données du formulaire et stocker dans $ingredient*/
            $manager->persist($ingredient);/* Préparer à evoyer dans la bdd les informations dans $ingredient*/
            $manager->flush();/* Envoyer dans la base de données */
            $this->addFlash( /** Le petit message flash comme dans AUGURE pour dire que c'est enregistrer*/
                'success',/** Le nom du message pour être récupérer dans le twig */
                'Votre ingrédient a été créé avec succès' /**Le contenu du message */
            );
            return $this->redirectToRoute('ingredient.index'); /**Atteindre la vue qui à pour nom ingredient.index  ici c'est l'affichage des données dans la table*/
            }
        
        return $this->render('pages/ingredient/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/suppression/{id}',name:'ingredient.delete',methods:['GET','POST'])]
    public function delete(
        EntityManagerInterface $manager,
        Ingredient $ingredient
        ):Response
    {
        if(!$ingredient ){/**Si l'ingrédient n'existe pas */
            $this->addFlash(
                'error',
                'Ingrédient non trouvé' /**Le contenu du message */
            );
            return $this->redirectToRoute('ingredient.index');
        }
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash( /** Le petit message flash comme dans AUGURE pour dire que c'est enregistrer*/
            'success',/** Le nom du message pour être récupérer dans le twig */
            'Votre ingrédient a été supprimé avec succès' /**Le contenu du message */
        );
        return $this->redirectToRoute('ingredient.index'); /**Atteindre la vue qui à pour nom ingredient.index  ici c'est l'affichage des données dans la table*/
        
        return 1;

    }
}
