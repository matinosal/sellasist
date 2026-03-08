<?php

namespace App\Request\ValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsControllerArgumentValueResolver(priority: 10)]
final class QueryStringValueResolver extends AbstractValueResolver implements ValueResolverInterface
{
    private Serializer $serializer;

    public function __construct() {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $metdaDataAwereNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $normalizer = new ObjectNormalizer(null, $metdaDataAwereNameConverter);
        $this->serializer = new Serializer([$normalizer]);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();
        if(!$type) {
            return [];
        }

        $queryData = $request->query->all();
        if (!$queryData) {
            return [];
        }
        
        try {
            $result = $this->serializer->denormalize($queryData, $type, context: [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]);
            $this->validateResult($result, options: ['groups' => 'query']);
        } catch (ValidationFailedException $e) {
            throw $e;
        }

        return [$result];
    }
}