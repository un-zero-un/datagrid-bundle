<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Datagrid\Form\Type;

use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UnZeroUn\Datagrid\Action\BatchAction;
use UnZeroUn\Datagrid\Datagrid\Form\Model\DatagridBatchAction;

class DatagridBatchActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $items = $options['items'];

        if (!is_array($items)) {
            $items = iterator_to_array($items);
        }

        $builder->add(
            'items',
            ChoiceType::class,
            [
                'choices'      => $items,
                'expanded'     => true,
                'multiple'     => true,
                'choice_value' => 'id',
                'choice_label' => function ($item) {
                    return (string)$item;
                },
            ]
        );

        $builder->add(
            'action',
            ChoiceType::class,
            [
                'choices'      => $options['actions'],
                'multiple'     => false,
                'expanded'     => false,
                'choice_value' => 'label',
                'choice_label' => 'label',
            ]
        );

        $builder->add('submit', SubmitType::class, ['label' => 'Valider']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => DatagridBatchAction::class,
            ]
        );

        $resolver->setRequired(
            [
                'items',
                'actions',
            ]
        );

        $resolver->setAllowedTypes('items', ['iterable']);
        $resolver->setAllowedTypes('actions', ['array']);

        $resolver->setNormalizer(
            'actions',
            function (OptionsResolver $resolver, iterable $actions) {
                foreach ($actions as $action) {
                    if (!$action instanceof BatchAction) {
                        throw new InvalidTypeException(
                            sprintf(
                                'config "actions" must be an iterable collection of "%s" instances',
                                BatchAction::class
                            )
                        );
                    }
                }

                return $actions;
            }
        );
    }
}
