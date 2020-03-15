<?php

namespace App\Form;

use App\Entity\Borrowing;
use App\Repository\EquipmentRepository;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class BorrowingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startedOn',DateType::class,[
                'label'=> 'Date de début de l\'emprunt',
                'widget'=>'single_text'
            ])
            ->add('allowedDays')
            ->add('borrowedBy', EntityType::class,[
                'class'=> User::class,
                'choice_label'=>'username',
                'attr'=>["data-live-search"=>true],
                'placeholder'=>'Qui emprunte ?'
            ])
            ->add('quantity');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Borrowing::class,
        ]);
    }
}
