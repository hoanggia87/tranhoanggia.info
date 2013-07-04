<?php
class User_Form_RegistrationForm extends Zend_Form
{
    public function init()
    {
    	$fullname = $this->createElement('text','full_name');
        $fullname->setLabel('Full Name:')
                    ->setRequired(false);
                    
        
                    
        $email = $this->createElement('text','email');
        $email->setLabel('Email: *')
                ->setRequired(false);                       
                
        $password = $this->createElement('password','password');
        $password->setLabel('Password: *')
                ->setRequired(true);
                
        $confirmPassword = $this->createElement('password','confirmPassword');
        $confirmPassword->setLabel('Confirm Password: *')
                ->setRequired(true);
                
        $register = $this->createElement('submit','register');
        $register->setLabel('Sign up')
                ->setIgnore(true);
                
        $this->addElements(array(
                        $fullname,                        
                        $email,
                        $username,
                        $password,
                        $confirmPassword,
                        $register
        ));
    }
}