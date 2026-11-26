<?php
class Acronimo
{
    public function __construct(private string $frase)
    {
    }

    public function generar(): string
    {
        $palabras = preg_split('/\s+/u', $this->frase, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $iniciales = '';
        foreach ($palabras as $palabra) {

            if (preg_match('/[\p{L}\p{N}]/u', $palabra, $m)) {
                $iniciales .= mb_strtoupper($m[0]);
            }
        }
        return $iniciales;
    }
}
