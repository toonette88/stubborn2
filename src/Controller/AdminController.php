<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/backoffice', name: 'admin_backoffice')]
    public function backoffice(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Liste des produits
        $products = $entityManager->getRepository(Product::class)->findAll();

        // Formulaire d'ajout de produit
        $newProduct = new Product();
        $addForm = $this->createForm(ProductType::class, $newProduct);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            // Gestion de l'image uploadée
            if ($file = $addForm->get('image')->getData()) {
                $newFilename = uniqid() . '.' . $file->guessExtension();  // Crée un nom unique pour chaque image
                $destination = $this->getParameter('kernel.project_dir') . '/assets/images';  // Dossier de destination
                try {
                    $file->move($destination, $newFilename);  // Déplace le fichier dans assets/images
                    $newProduct->setImage($newFilename);  // Enregistre le nom du fichier dans l'entité
                } catch (FileException $e) {
                    // Gérer les erreurs d'upload si nécessaire
                }
            }

            $entityManager->persist($newProduct);
            $entityManager->flush();

            return $this->redirectToRoute('admin_backoffice');
        }

        // Préparez un tableau de formulaires pour chaque produit
        $editForms = [];
        foreach ($products as $product) {
            $form = $this->createForm(ProductType::class, $product);
            $editForms[$product->getId()] = $form->createView();
        }

        return $this->render('admin/backoffice.html.twig', [
            'add_form' => $addForm->createView(),
            'products' => $products,
            'edit_forms' => $editForms, // Envoi des formulaires d'édition
        ]);
}

    #[Route('/product/{id}/edit', name: 'admin_product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créez le formulaire à partir de l'entité
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image uploadée
            $file = $form->get('image')->getData();
            if ($file) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $destination = $this->getParameter('kernel.project_dir') . '/assets/images';
                $file->move($destination, $newFilename);
                $product->setImage($newFilename);
            }

            $entityManager->flush(); // Sauvegarde de l'entité modifiée
            return $this->redirectToRoute('admin_backoffice');
        }

        return $this->render('admin/backoffice.html.twig', [
            'form' => $form->createView(), // Assurez-vous de passer la vue du formulaire
        ]);
    }

    #[Route('/product/{id}/delete', name:'admin_product_delete')]
    public function deleteProduct(Product $product, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Supprimer le produit
        $entityManager->remove($product);
        $entityManager->flush();

        // Ajout d'un message flash pour notifier la suppression
        $this->addFlash('success', 'Produit supprimé avec succès.');

        // Redirection vers la liste des produits
        return $this->redirectToRoute('admin_backoffice');
    }
}
