<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class ArticleController extends AbstractController
{
    // injection de dépendance
    #[Route('/articles', name: 'listes_articles')]
    public function listeArticle(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();


        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/detail-article/{id}', name: 'detail_article')]
    public function detailArticle(ArticleRepository $repo, $id): Response
    {
        $article = $repo->find($id);


        return $this->render('article/detail-article.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/ajout-article', name: 'ajout_article')]
    #[Route('/modification-article/{id}', name: 'modification-article')]

    public function ajoutArticle(Article $article = null, Request $request, EntityManagerInterface $manager): Response
    {

        if (!$article) {
            $article = new Article();
        }

        $formu = $this->createFormBuilder($article)
            ->add('designation', TextType::class, ['attr' => ['class' => 'form-control']])

            ->add('description', TextareaType::class, ['attr' => ['class' => 'form-control']])

            ->add('prix', NumberType::class, ['attr' => ['class' => 'form-control']])

            ->add('image', FileType::class, [
                'required' => false, 'label' => 'Image principale', 'mapped' => false, 'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Seul les fichier jpg et png sont valides'
                    ])
                ]
            ])

            ->add('prix', NumberType::class, ['attr' => ['class' => 'form-control']])

            ->add('categorie', EntityType::class, ['class' => Categorie::class, 'choice_label' => 'designation', 'label' => 'Categorie', 'attr' => ['class' => 'form-control']])

            ->add('save', SubmitType::class, ['label' => 'Enregistrer', 'attr' => ['class' => 'btn btn-primary']])

            ->getForm();

        $formu->handleRequest($request);

        if ($formu->isSubmitted() && $formu->isValid()) {

            $article->setDateCreation(new \DateTime());

            // dd(($formu->get('image')->getData())) comme un vardump en mieux

            // si l'utilisateur a selectionné une image
            if ($formu->get('image')->getData()) {

                // on récupère le nom original de l'image (toto)
                $image = $formu->get('image')->getData();

                $nomOriginal = pathinfo($image->getclientOriginalName(), PATHINFO_FILENAME);

                // on créait un nouveau nom unique (toto_azeertyuiop)
                $nouveauNom = $nomOriginal . "_" . uniqid() . "." . $image->guessExtension();

                $image->move($this->getParameter('uploads'), $nouveauNom);

                $article->setNomImage($nouveauNom);
            }

            $manager->persist($article);
            $manager->flush();
        }


        return $this->render('article/ajout-article.html.twig', [
            'formulaire' => $formu->createView(),
            'article' => $article,
            'edition' => $article->getId() != null

        ]);
    }
}
