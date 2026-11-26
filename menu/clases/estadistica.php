<?php
class Estadistica
{
    private array $datos;

    public function __construct(array $datos)
    {
        if (!$datos) {
            throw new InvalidArgumentException('Ingresa al menos un número.');
        }
        foreach ($datos as $dato) {
            if (!is_numeric($dato) || !is_finite((float) $dato)) {
                throw new InvalidArgumentException("«{$dato}» no es un número válido.");
            }
        }
        $this->datos = array_map('floatval', $datos);
        sort($this->datos);
    }

    public function promedio(): float
    {
        return array_sum($this->datos) / count($this->datos);
    }

    public function mediana(): float
    {
        $n = count($this->datos);
        $mitad = intdiv($n, 2);
        return $n % 2 ? $this->datos[$mitad] : ($this->datos[$mitad - 1] + $this->datos[$mitad]) / 2;
    }

    public function moda(): array
    {
        $frecuencias = array_count_values(array_map('strval', $this->datos));
        $maxima = max($frecuencias);
        return $maxima > 1 ? array_keys($frecuencias, $maxima) : [];
    }
}
