<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('contry', EntityType::class, [
                'placeholder' => 'Choice a country', 
                'class' => Country::class,
                'choice_label' => 'name',
                'query_builder' => function(CountryRepository $countryRepo){
                    return $countryRepo->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                }
             ] )
            ->add('city', EntityType::class, [
                'placeholder' => 'Choice a City', 
                'class' => City::class,
                'choice_label' => 'name',
                'query_builder' => function(CityRepository $countryRepo){
                    return $countryRepo->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                }
             ] )
            ->add('message', TextareaType::class)
            ->getForm();

        return $this->render('home.html.twig',['form' => $form->createView() ]);
    }
}
