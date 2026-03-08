<?php

namespace App\Request\Utils;

class FormDataConvert
{
    public function convert(object $object): array
    {
        $reflection = new \ReflectionObject($object);
        $dataArray = [];
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true); 
            $value = $property->getValue($object);

            if (is_scalar($value)) {
                $dataArray[$property->getName()] = $value;
            }
        }

        $multipart = [];
        foreach ($dataArray as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }
    }
}