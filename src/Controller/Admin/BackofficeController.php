<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class BackofficeController extends AbstractController
{
    #[Route('/backoffice', name: 'admin_backoffice')]
    public function backoffice(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Formulaire d'ajout d'un nouveau produit
        $newProduct = new Product();
        $addForm = $this->createForm(ProductType::class, $newProduct);
        $addForm->handleRequest($request);
    
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $entityManager->persist($newProduct);
            $entityManager->flush();
    
            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('admin_backoffice');
        }
    
        // Récupération des produits existants
        $products = $entityManager->getRepository(Product::class)->findAll();
    
        // Gestion des formulaires d'édition
        foreach ($products as $product) {
            $editForm = $this->createForm(ProductType::class, $product, [
                'action' => $this->generateUrl('admin_product_edit', ['id' => $product->getId()]),
                'method' => 'POST',
            ]);
            $product->editForm = $editForm->createView(); // Ajout de la vue du formulaire
        }
    
        return $this->render('admin/backoffice.html.twig', [
            'products' => $products,
            'addForm' => $addForm->createView(),
        ]);
    }
    
    #[Route('/product/{id}/edit', name: 'admin_product_edit', methods: ['POST'])]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $editForm = $this->createForm(ProductType::class, $product);
        $editForm->handleRequest($request);
    
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Produit modifié avec succès.');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de la modification.');
        }
    
        return $this->redirectToRoute('admin_backoffice');
    }
    

    #[Route('/product/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function delete(Product $product, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérification du token CSRF
        if (!$this->isCsrfTokenValid('delete_product_' . $product->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin_backoffice');
        }
    
        // Suppression du produit
        $entityManager->remove($product);
        $entityManager->flush();
    
        $this->addFlash('success', 'Produit supprimé avec succès.');
        return $this->redirectToRoute('admin_backoffice');
    }
}
