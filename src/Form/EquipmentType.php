<?php

namespace App\Form;

use App\Entity\Equipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', null, ['attr' => [
                'placeholder' => 'Description',
            ]])
            ->add('quantity')
            ->add('allowedDays')
            ->add('imageFile', FileType::class,[
                'label'=>false,
                'mapped'=>false,
                'required'=>false,
                'constraints' => [
                    new File([
                        'maxSize' => '500000',
                        'mimeTypes' => [
                            'image/jpeg'],
                        'mimeTypesMessage' => 'L\'image doit être de maximum 500Ko et au format jpg ou jpeg !',
                        ])
                    ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Equipment::class,
        ]);
    }
}
