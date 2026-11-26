<?php
class Calculadora
{
    public function __construct(
        private float $a,
        private float $b,
        private string $operacion
    ) {
    }

    public function operar(): float
    {
        if ($this->operacion === 'division' && $this->b == 0) {
            throw new InvalidArgumentException('No se puede dividir entre cero.');
        }
        return match ($this->operacion) {
            'suma' => $this->a + $this->b,
            'resta' => $this->a - $this->b,
            'multiplicacion' => $this->a * $this->b,
            'division' => $this->a / $this->b,
            'porcentaje' => $this->a * $this->b / 100,
            default => throw new InvalidArgumentException('Operación inválida.'),
        };
    }
}
