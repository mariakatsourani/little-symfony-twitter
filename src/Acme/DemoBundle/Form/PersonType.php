<?php
namespace Acme\DemoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PersonType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $oprtions){
        $builder->add('email', 'email')->add('fullname', 'text')->add('submit', 'submit');

    }

    public function getName(){
        return 'person';
    }
}