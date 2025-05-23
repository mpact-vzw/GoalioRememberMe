<?php

namespace GoalioRememberMe\Form;

use LmcUser\Options\AuthenticationOptionsInterface;
use LmcUser\Form\Login as LoginForm;

class Login extends LoginForm
{
    /**
     * @var AuthenticationOptionsInterface
     */
    protected $authOptions;

    public function __construct($name = null, AuthenticationOptionsInterface $options = null)
    {
        parent::__construct($name, $options);

        $this->add(array(
            'type' => 'Laminas\Form\Element\Checkbox',
            'name' => 'remember_me',
            'options' => array(
                'label' => 'Stay logged in',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
    }
}
