<?php
class Binario
{
    public function __construct(private int $numero)
    {
        if ($numero === PHP_INT_MIN) {
            throw new InvalidArgumentException('Ese número es demasiado grande.');
        }
    }

    public function convertir(): string
    {
        return ($this->numero < 0 ? '-' : '') . decbin(abs($this->numero));
    }
}
