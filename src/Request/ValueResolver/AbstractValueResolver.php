<?php

namespace App\Request\ValueResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

abstract class AbstractValueResolver
{
    protected function validateResult(mixed $dto, array $options, Contraint|array|null $constraints = null): void
    {
        if ($options['disable_validation'] ?? false) {
            return;
        }

        $validatorOptions = $this->getValidatorOptions($options);
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $constraintsViolationList = $validator->validate($dto, $constraints, $validatorOptions['groups'] ?? null);

        if (0 !== count($constraintsViolationList)) {
            throw new ValidationFailedException($dto, $constraintsViolationList);
        }
    }

    private function getValidatorOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'groups' => false,
            'traverse' => false,
            'deep' => false,
        ]);

        return $resolver->resolve($options['validator'] ?? []);
    }
}