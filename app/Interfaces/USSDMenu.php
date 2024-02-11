<?php

namespace App\Interfaces;

interface USSDMenu
{
    public static function menu(USSDRequest $request, array $sessionData): array;
}
