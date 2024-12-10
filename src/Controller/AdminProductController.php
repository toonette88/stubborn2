<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/product')]
class AdminProductController extends AbstractController
{
    #[Route('/add', name: 'admin_product_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $newProduct = new Product();
        $form = $this->createForm(ProductType::class, $newProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($file = $form->get('image')->getData()) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $destination = $this->getParameter('kernel.project_dir') . '/assets/images';

                try {
                    $file->move($destination, $newFilename);
                    $newProduct->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $entityManager->persist($newProduct);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');

            return $this->redirectToRoute('admin_backoffice');
        }

        return $this->render('admin/product_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créez le formulaire d'édition lié à l'entité Product existante
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Si un fichier image est téléchargé, le gérer comme dans le formulaire d'ajout
            $file = $form->get('image')->getData();
            if ($file) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $destination = $this->getParameter('kernel.project_dir') . '/assets/images';
    
                try {
                    $file->move($destination, $newFilename);
                    $product->setImage($newFilename);  // Modifiez l'image du produit existant
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }
    
            // Mettre à jour l'entité product
            $entityManager->flush();
            $this->addFlash('success', 'Produit modifié avec succès.');
    
            return $this->redirectToRoute('admin_backoffice');  // Redirige vers le backoffice après la modification
        }
    
        // Rendu du formulaire d'édition
        return $this->render('admin/product_edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,  // Passez l'entité produit existante au template
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_product_delete')]
    public function delete(Product $product, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès.');

        return $this->redirectToRoute('admin_backoffice');
    }
}
