<?php

namespace App\Services;

class DataNormalizer{

    public function normalize(array $data): array{
        foreach ($data as $key => $value){
            if(is_array($value)){
                $data[$key] = $this->normalize($value);

            }elseif(is_string($value)){

                $data[$key] = $this->cleanString($value);

            }
        }
        return $data;
    }


    protected function cleanString(string $string): string{

        $string = strtolower($string);

        $string = trim($string); //quitar espacios

        //confirmar si hay que añadir algo mas
        return $string;

    }


}