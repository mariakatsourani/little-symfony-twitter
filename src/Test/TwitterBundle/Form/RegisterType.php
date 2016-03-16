<?php
namespace Test\TwitterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegisterType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('username', 'text');
        $builder->add('password', 'password');
        //$builder->add('repeat_password', 'password');
        $builder->add('email', 'email');
        $builder->add('Register', 'submit');
    }

    public function getName(){
        return 'user';
    }
}