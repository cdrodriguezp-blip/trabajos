<?php
class Factorial
{
    public function __construct(private int $numero)
    {
    }

    public function factorial(): int
    {

        if ($this->numero < 0 || $this->numero > 20) {
            throw new InvalidArgumentException('El factorial se calcula para enteros entre 0 y 20.');
        }
        $resultado = 1;
        for ($i = 2; $i <= $this->numero; $i++) {
            $resultado *= $i;
        }
        return $resultado;
    }

    public function fibonacci(): array
    {

        if ($this->numero < 1 || $this->numero > 90) {
            throw new InvalidArgumentException('Fibonacci se calcula para entre 1 y 90 términos.');
        }
        $serie = [];
        for ($i = 0; $i < $this->numero; $i++) {
            $serie[] = $i < 2 ? $i : $serie[$i - 1] + $serie[$i - 2];
        }
        return $serie;
    }
}
