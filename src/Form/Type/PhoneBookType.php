<?php
/*
 * Copyright 2014 IMAGIN Sp. z o.o. - imagin.pl
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Type;

use App\Entity\PhoneBook;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneBookType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'phoneRecords',
            CollectionType::class,
            array(
                'entry_type' => PhoneRecordType::class,
                'allow_add' => true,
                'allow_delete' => true,
            )
        );

        $builder->add(
            'urlRecords',
            CollectionType::class,
            array(
                'entry_type' => UrlRecordType::class,
                'allow_add' => true,
                'allow_delete' => true,
            )
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => PhoneBook::class,
            )
        );
    }
}
