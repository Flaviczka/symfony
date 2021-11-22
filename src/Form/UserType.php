<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UserType extends AbstractType
{
    /** 
     *@var TranslatorInterface 
     * 
     */
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans("user.email")
            ])
            ->add('roles', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'choices' => [
                        $this->translator->trans("user.admin") => 'ROLE_ADMIN',
                        $this->translator->trans("user.user") => 'ROLE_USER'
                    ],
                    'label' => false,
                    'expanded' => false,
                    'multiple' => false
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'error_bubbling' => true,
                'invalid_message' => 'm!m FO SESIR 2 FOI LE MEM',
                'first_options' => ['label' => $this->translator->trans("user.password")],
                'second_options' => ['label' => $this->translator->trans("user.repeat_password")],
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans("general.button.success"),
                'attr' => [
                    'class' => 'btn-light'
                ]
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
