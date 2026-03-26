<?php

namespace App\Services\Rules;

//Interfaz para que todas las reglas tengan el método evaluate

interface DetectionRuleInterface
{

    public function setSettings(array $settings): void;

    public function evaluate(array $data): ?array; //devuelve 1 o 0
}