<?php

namespace GoalioRememberMe\Mapper;

use Laminas\Hydrator\ClassMethodsHydrator;

class RememberMeHydrator extends ClassMethodsHydrator
{
    public function extract($object) : array
    {
        $data = parent::extract($object);
        return $data;
    }

    public function hydrate(array $data, $object)
    {
        return parent::hydrate($data, $object);
    }
}
