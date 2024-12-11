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
            // Mettre à jour la date de mise à jour
            $newProduct->setUpdatedAt(new \DateTimeImmutable());

            // Gestion de l'image
            $file = $form->get('imageFile')->getData();
            if ($file) {
                // Vérification du type de fichier
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                    $this->addFlash('error', 'Le type de fichier n\'est pas autorisé.');
                    return $this->redirectToRoute('admin_product_add');
                }

                $newFilename = uniqid() . '.' . $file->guessExtension();
                $destination = $this->getParameter('kernel.project_dir') . '/assets/images';

                // Créer le dossier s'il n'existe pas
                if (!is_dir($destination)) {
                    mkdir($destination, 0777, true);
                }

                try {
                    $file->move($destination, $newFilename);
                    $newProduct->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
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
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour la date de mise à jour
            $product->setUpdatedAt(new \DateTimeImmutable());

            // Gestion de l'image
            $file = $form->get('imageFile')->getData();
            if ($file) {
                $destination = $this->getParameter('kernel.project_dir') . '/assets/images';

                // Supprimer l'ancienne image si nécessaire
                $oldImage = $product->getImage();
                if ($oldImage && file_exists($destination . '/' . $oldImage)) {
                    unlink($destination . '/' . $oldImage);
                }

                // Vérification du type de fichier
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                    $this->addFlash('error', 'Le type de fichier n\'est pas autorisé.');
                    return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
                }

                $newFilename = uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move($destination, $newFilename);
                    $product->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Produit modifié avec succès.');

            return $this->redirectToRoute('admin_backoffice');
        }

        return $this->render('admin/_product_edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'image' => $product->getImage(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_product_delete')]
    public function delete(Product $product, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Suppression de l'image associée
        $imagePath = $this->getParameter('kernel.project_dir') . '/assets/images/' . $product->getImage();
        if ($product->getImage() && file_exists($imagePath)) {
            unlink($imagePath);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès.');

        return $this->redirectToRoute('admin_backoffice');
    }
}
