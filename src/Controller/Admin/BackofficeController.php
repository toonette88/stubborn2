<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class BackofficeController extends AbstractController
{
    #[Route('/backoffice', name: 'admin_backoffice')]
    public function backoffice(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Création du formulaire pour ajouter un produit
        $newProduct = new Product();
        $addForm = $this->createForm(ProductType::class, $newProduct);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $file = $addForm->get('image')->getData();
            if ($file) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $destination = $this->getParameter('kernel.project_dir') . '/assets/images';

                try {
                    $file->move($destination, $newFilename);
                    $newProduct->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $entityManager->persist($newProduct);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('admin_backoffice');
        }

        $products = $entityManager->getRepository(Product::class)->findAll();

        $editForms = [];
        foreach ($products as $product) {
            $form = $this->createForm(ProductType::class, $product);
            $editForms[$product->getId()] = $form->createView();
        }

        return $this->render('admin/backoffice.html.twig', [
            'products' => $products,
            'edit_forms' => $editForms,
            'addForm' => $addForm->createView(), 
        ]);
    }
}
