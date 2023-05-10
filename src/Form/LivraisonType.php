<?php

namespace App\Form;

use App\Entity\Livraison;
use App\Entity\Panier;
use App\Repository\PanierRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class LivraisonType extends AbstractType
{
    private $security;
    private $panierRepository;

    public function __construct(Security $security, PanierRepository $panierRepository)
    {
        $this->security = $security;
        $this->panierRepository = $panierRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $total = 0;
        $isNewLivraison = $builder->getData() instanceof Livraison && $builder->getData()->getId() === null;

        if ($isNewLivraison) {
            $paniers = $this->panierRepository->findBy(['user' => $this->security->getUser()]);

            foreach ($paniers as $panier) {
                $total += $panier->getProduit()->getPrix() * $panier->getQuantite();
            }
        } else {
            $total = $builder->getData()->getTotal();
        }

        $builder
            ->add('adresse')
            ->add('date_livraison')
            ->add('statut', null, [
                'data' => 'en cours',
                'disabled' => true,
            ])
            ->add('total', TextType::class, [
                'data' => $total,
                'disabled' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livraison::class,
        ]);
    }
}