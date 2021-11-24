<?php

namespace App\Form;

use DateTime;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\Status;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class TaskType extends AbstractType
{
    /**
     * Undocumented variable
     *
     * @var StatusRepository
     */
    private $repository;

    /**
     * Undocumented variable
     *
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator, StatusRepository $repository)
    {
        $this->translator = $translator;
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->translator->trans('general.name')
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->translator->trans('general.description')
            ])
            ->add('dueAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => $this->translator->trans('general.due_date')
            ])
            ->add('tag', EntityType::class, [
                'label' => $this->translator->trans('general.category'),
                'class' => Tag::class, 'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                }, 'choice_label' => 'name'
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    $this->translator->trans("general.status.1") => $this->repository->find(1),
                    $this->translator->trans("general.status.2") => $this->repository->find(2),
                    $this->translator->trans("general.status.3") => $this->repository->find(3)
                ],
                'label' => $this->translator->trans("general.status.title"),
                'expanded' => false,
                'multiple' => false
            ])



            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('general.button.success'), 'attr' => ['btn']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
