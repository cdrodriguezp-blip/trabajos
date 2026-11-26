<?php
class Nodo
{
    public ?Nodo $izq = null;
    public ?Nodo $der = null;

    public function __construct(public string $valor)
    {
    }
}
