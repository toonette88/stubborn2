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
        $newProduct = new Product();
        $addForm = $this->createForm(ProductType::class, $newProduct);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $entityManager->persist($newProduct);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('admin_backoffice');
        }

        $products = $entityManager->getRepository(Product::class)->findAll();

        $editForms = [];
        foreach ($products as $product) {
            $editForms[$product->getId()] = $this->createForm(ProductType::class, $product)->createView();
        }
        
        return $this->render('admin/backoffice.html.twig', [
            'products' => $products,
            'addForm' => $addForm->createView(),
            'edit_forms' => $editForms,
        ]);
    }
}
