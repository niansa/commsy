<?php
namespace CommsyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use CommsyBundle\Form\Type\Event\AddBibliographicFieldListener;

class MaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'constraints' => array(
                    new NotBlank(),
                ),
                'label' => 'title',
                'attr' => array(
                    'placeholder' => 'title',
                    'class' => 'uk-form-width-medium cs-form-title',
                ),
                'translation_domain' => 'material',
            ))
            ->add('permission', CheckboxType::class, array(
                'label' => 'permission',
                'required' => false,
                'translation_domain' => 'form',
            ))
            ->add('editor_switch', CheckboxType::class, array(
                'label' => 'editor_switch',
                'required' => false,
                'translation_domain' => 'form',
            ))
            ->add('biblio_select', ChoiceType::class, array(
                'choices'  => array(
                    'BiblioPlainType' => 'plain',
                    'BiblioBookType' => 'book',
                    'BiblioCollectionType' => 'collection',
                    'BiblioArticleType' => 'article',
                    'BiblioJournalType' => 'journal',
                    'BiblioChapterType' => 'chapter',
                    'BiblioNewspaperType' => 'newspaper',
                    'BiblioThesisType' => 'thesis',
                    'BiblioManuscriptType' => 'manuscript',
                    'BiblioWebsiteType' => 'website',
                    'BiblioDocManagementType' => 'document management',
                    'BiblioPictureType' => 'picture',

                ),
                'label' => 'bib reference',
                'choice_translation_domain' => true,
                'required' => false,
                'translation_domain' => 'form'
            ))
            ->addEventSubscriber(new AddBibliographicFieldListener())
            ->add('save', SubmitType::class, array(
                'attr' => array(
                    'class' => 'uk-button-primary',
                ),
                'label' => 'save',
                'translation_domain' => 'form',
            ))
            ->add('cancel', SubmitType::class, array(
                'attr' => array(
                    'class' => 'uk-button-primary',
                    'formnovalidate' => '',
                ),
                'label' => 'cancel',
                'translation_domain' => 'form',
            ))
        ;
        
    }

    /**
     * Configures the options for this type.
     * 
     * @param  OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([])
        ;
    }

    /**
     * Returns the prefix of the template block name for this type.
     * The block prefix defaults to the underscored short class name with the "Type" suffix removed
     * (e.g. "UserProfileType" => "user_profile").
     * 
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'material';
    }
}