<?php

namespace App\Form;

use App\Entity\Article;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', CKEditorType::class,[
                'config'=>[
                    'uiColor'=>"18BC9C",
                    'toolbar'=>'standard',
                    'required'=>true,
                    'language'=> 'fr',

                    //ici le correcteur d'orthographe
                    'scayt_autoStartup' => true
                ]
            ])
//intégraiton de l'éditeur de texte pour le champ contenu.
            ->add('content',CKEditorType::class,[
                'config'=>[
                    'uiColor'=>"18BC9C",
                    'toolbar'=>'standard',
                    'required'=>true,
                    'language'=> 'fr',
                    //ici le correcteur d'orthographe
                    'scayt_autoStartup' => true,

                ]
            ])
           // ->add('image',UrlType::class)
            ->add('imageFile', FileType::class,[
                'required'=>false])
            ->add('publishedAt',DateType::class)
            ->add ('isPrivate')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
