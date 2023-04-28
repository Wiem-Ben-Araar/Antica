<?php

namespace App\Form;

use App\Entity\Panier;
use App\Entity\Produits;
use App\Repository\PanierRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Security;


class PanierType extends AbstractType
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
        {
            $paniers = $this->panierRepository->findBy(['user' => $this->security->getUser()]);
            $total = 0;
            foreach ($paniers as $panier) {
                $total += $panier->getProduit()->getPrix() ;
            }
            $builder
                ->add('produit')
                ->add('total', TextType::class, [
                    'data' => $total,
                    'disabled' => true,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Panier::class,
        ]);
    }

    public function isValid()
    {
        $quantite = $this->get('quantite')->getData();
        $total = $this->get('total')->getData();

        if ($quantite <= 0) {
            $this->get('quantite')->addError(new FormError('La quantité doit être supérieure à 0.'));
        }

        if ($total <= 0) {
            $this->get('total')->addError(new FormError('Le total doit être supérieur à 0.'));
        }

        return parent::isValid();
    }

}
