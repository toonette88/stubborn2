<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    #[IsGranted('ROLE_USER')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $priceRanges = [
            '10-29' => [10, 29],
            '30-35' => [30, 35],
            '35-50' => [35, 50],
        ];

        // Récupérer la sélection de l'utilisateur
        $selectedRange = $request->query->get('priceRange');

        if ($selectedRange && isset($priceRanges[$selectedRange])) {
            [$minPrice, $maxPrice] = $priceRanges[$selectedRange];
            $products = $entityManager->getRepository(Product::class)->findByPriceRange($minPrice, $maxPrice);
        } else {
            $products = $entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'priceRanges' => $priceRanges,
            'selectedRange' => $selectedRange,
        ]);
    }
}
