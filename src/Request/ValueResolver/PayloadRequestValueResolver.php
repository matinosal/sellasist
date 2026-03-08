<?php

namespace App\Request\ValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use App\Utils\Serializer\Normalizer\EnumDenormalizer;

#[AsControllerArgumentValueResolver(priority: 100)]
class PayloadRequestValueResolver extends AbstractValueResolver implements ValueResolverInterface
{
    private Serializer $serializer;

    public function __construct() {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $metdaDataAwereNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $metdaDataAwereNameConverter, null, new ReflectionExtractor());
        $this->serializer = new Serializer([new BackedEnumNormalizer(), new ArrayDenormalizer(), $objectNormalizer]);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();
        if(!$type) {
            return [];
        }

        $data = $request->getContent();
        if ($data === '') {
            return [];
        }

        $data = json_decode($data, true);
        try {
            $result = $this->serializer->denormalize($data, $type, context: [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]);
            $this->validateResult($result, options: ['groups' => 'payload']);
        } catch (ValidationFailedException $e) {
            throw $e;
        }

        return [$result];
    }
}