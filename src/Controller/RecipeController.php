<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index', methods:['GET'])]
    public function index(
        RecipeRepository $repository, /* Pour faire des requêtes sur une table à partir des entités mappées sur cette même table*/
        PaginatorInterface $paginator, /** Pour la pagination des résultats à afficher */
        Request $request /* Pour faire une requête */
    ): Response
    {
        $query = $repository->findAll();

        $recipe = $paginator->paginate(
            /** et mettre une mise en forme pour s'adapter à la pagination En dessous générer automatiquement */
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/);


        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipe,
        ]);
    }

    #[Route('/recette/nouveau',name:'recipe.new',methods:['GET','POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $manager
        ):Response
    {
        
        $recipe = new Recipe();        
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);         

        if($form->isSubmitted() && $form->isValid()){ 
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash( 
                'success',
                'Votre recette a été créé avec succès' 
            );
            return $this->redirectToRoute('recipe.index');             
        }

        return $this->render('pages/recipe/new.html.twig',[ 
            'form' => $form->createView()
        ]);
    }

    #[Route('recette/edition/{id}', name:'recipe.edit',methods:['GET','POST'])]
    public function edit(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $manager
    ):Response
    {        
          $form = $this->createForm(RecipeType::class, $recipe);
          $form->handleRequest($request); 

          if($form->isSubmitted() && $form->isValid()){ 
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash( 
                'success',
                'Votre recette a été modifié avec succès'
            );
            return $this->redirectToRoute('recipe.index'); 
            }
        
        return $this->render('pages/recipe/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/recipe/suppression/{id}',name:'recipe.delete',methods:['GET','POST'])]
    public function delete(
        EntityManagerInterface $manager,
        Recipe $recipe
        ):Response
    {
        if(!$recipe ){/**Si l'ingrédient n'existe pas */
            $this->addFlash(
                'error',
                'Recette non trouvé' /**Le contenu du message */
            );
            return $this->redirectToRoute('recipe.index');
        }
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash( /** Le petit message flash comme dans AUGURE pour dire que c'est enregistrer*/
            'success',/** Le nom du message pour être récupérer dans le twig */
            'Votre recette a été supprimé avec succès' /**Le contenu du message */
        );
        return $this->redirectToRoute('recipe.index'); /**Atteindre la vue qui à pour nom ingredient.index  ici c'est l'affichage des données dans la table*/
        
        return 1;

    }
    
}
