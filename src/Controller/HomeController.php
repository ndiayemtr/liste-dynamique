<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
use Monolog\DateTimeImmutable;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints as Assert; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, CountryRepository $countryRepos, CityRepository $cityRepos,  EntityManagerInterface $em): Response
    {
        //dd($countryRepos->find(1));
        $form = $this->createFormBuilder(['country' => $countryRepos->find(1)])
            ->add('name', TextType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom'])
                ],
            ])
            ->add('age', IntegerType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre age']),
                    new Positive()
                ],
            ])
            ->add('country', EntityType::class, [
                'placeholder' => 'Choice a country', 
                'class' => Country::class,
                'choice_label' => 'name',
                'query_builder' => function(CountryRepository $countryRepos){
                    return $countryRepos->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                }
             ] )
             ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $formEvent) use ($cityRepos){
                dd($formEvent->getData());

            })

             ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $formEvent) use ($cityRepos){
                $country = $formEvent->getData()['country'] ?? null;

                $cities = $country === null ? [] :  $cityRepos->findBy(['country' => $country], ['name' => 'ASC']);

                $formEvent->getForm()->add('city', EntityType::class, [
                    'class' => City::class,
                    'choice_label' => 'name',
                    'choices' => $cities,
                    'disabled' => $country === null,
                    'placeholder' => 'Choice a City', 
                    'constraints' => new NotBlank(['message' => 'Veuillez selectionner votre region']),
                ]);

            })

            ->add('message', TextareaType::class, [
                'attr' => ['rows' => 5],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre text']),
                    new Length(['min' => 5])
                ],
            ])
            ->add('availableAt', DateTimeType::class)
            /* ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $formEvent) use ($cityRepos) {
                  $country = $formEvent->getData()['country'] ?? null;

                  dd($country);

                if ($country !== null && $country->getId() == '1') {
                    $id = $country->getId();
                    $formEvent->getForm()->add('city', EntityType::class, [
                        'placeholder' => 'Choice a City', 
                        'class' => City::class,
                        'choice_label' => 'name',
                        'query_builder' => function() use ($cityRepos, $country){
                            return $cityRepos->createQueryBuilder('c')
                                    ->andWhere('c.country = :country')
                                    ->setParameter('country', $country)
                                    ->orderBy('c.name', 'ASC');
                        }
                     ] );
                    //$formEvent->getForm()->remove('message');
                } 
            }) */

            
        
        ->getForm();
        
       
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }
        
        
        return $this->render('home.html.twig',['form' => $form->createView() ]);
    }
}
