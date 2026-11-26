<?php

header('Content-Type: application/json; charset=utf-8');

foreach (['acronimo', 'factorial', 'estadistica', 'conjuntos', 'binario', 'arbol', 'calculadora'] as $clase) {
    require_once __DIR__ . "/clases/$clase.php";
}

function campo(string $nombre): string
{
    $valor = $_POST[$nombre] ?? '';
    return is_string($valor) ? $valor : '';
}

function lista(string $texto): array
{
    return preg_split('/[\s,;]+/', $texto, -1, PREG_SPLIT_NO_EMPTY);
}

function entero(string $texto): int
{
    $n = filter_var(trim($texto), FILTER_VALIDATE_INT);
    if ($n === false) {
        throw new InvalidArgumentException('Ingresa un número entero válido.');
    }
    return $n;
}

function decimal(string $texto): float
{
    if (!is_numeric($texto)) {
        throw new InvalidArgumentException('Ingresa números válidos.');
    }
    return (float) $texto;
}

function num(float $x): string
{
    return (string) (round($x, 8) + 0.0);
}

function conjunto(array $elementos): string
{
    return $elementos ? '{ ' . implode(', ', $elementos) . ' }' : '∅';
}

function procesar(): array
{
    switch (campo('punto')) {
        case '1':
            $frase = trim(campo('frase'));
            $acronimo = (new Acronimo($frase))->generar();
            if ($acronimo === '') {
                throw new InvalidArgumentException('Escribe una frase con al menos una palabra.');
            }
            return ['lineas' => [['Frase', $frase], ['Acrónimo', $acronimo]]];

        case '2':
            $n = entero(campo('numero'));
            $obj = new Factorial($n);
            if (campo('operacion') === 'fibonacci') {
                return ['lineas' => [['Términos', (string) $n], ['Sucesión de Fibonacci', implode(', ', $obj->fibonacci())]]];
            }
            return ['lineas' => [['Número', (string) $n], ['Factorial', (string) $obj->factorial()]]];

        case '3':
            $obj = new Estadistica(lista(campo('numeros')));
            $moda = $obj->moda();
            return ['lineas' => [
                ['Promedio', num($obj->promedio())],
                ['Mediana', num($obj->mediana())],
                ['Moda', $moda ? implode(', ', $moda) : 'Sin moda'],
            ]];

        case '4':
            $obj = new Conjuntos(lista(campo('conjuntoA')), lista(campo('conjuntoB')));
            $union = $obj->union();
            $inter = $obj->interseccion();
            $ab = $obj->diferenciaAB();
            $ba = $obj->diferenciaBA();
            return [
                'lineas' => [
                    ['Unión (A ∪ B)', conjunto($union), 'union'],
                    ['Intersección (A ∩ B)', conjunto($inter), 'interseccion'],
                    ['Diferencia (A − B)', conjunto($ab), 'a-b'],
                    ['Diferencia (B − A)', conjunto($ba), 'b-a'],
                ],
                'grafico' => ['tipo' => 'venn', 'soloA' => $ab, 'ambos' => $inter, 'soloB' => $ba],
            ];

        case '5':
            $n = entero(campo('numero'));
            return ['lineas' => [['Número decimal', (string) $n], ['Binario', (new Binario($n))->convertir()]]];

        case '6':
            $pre = lista(campo('preorden'));
            $in = lista(campo('inorden'));
            $post = lista(campo('postorden'));
            $arbol = new Arbol();
            $raiz = $arbol->construir($pre, $in, $post);
            $respuesta = [
                'lineas' => [
                    ['Preorden', implode(', ', $arbol->preorden($raiz))],
                    ['Inorden', implode(', ', $arbol->inorden($raiz))],
                    ['Postorden', implode(', ', $arbol->postorden($raiz))],
                ],
                'grafico' => ['tipo' => 'arbol', 'raiz' => $raiz],
            ];
            if (!$in) {
                $respuesta['nota'] = 'Con solo preorden y postorden pueden existir varios árboles; este es uno posible.';
            }
            return $respuesta;

        case '7':
            $a = decimal(campo('num1'));
            $b = decimal(campo('num2'));
            $operacion = campo('operacion');
            $resultado = num((new Calculadora($a, $b, $operacion))->operar());
            $simbolos = ['suma' => '+', 'resta' => '−', 'multiplicacion' => '×', 'division' => '÷', 'porcentaje' => '% de'];
            $cuenta = num($a) . ' ' . $simbolos[$operacion] . ' ' . num($b);
            return [
                'lineas' => [['Operación', $cuenta], ['Resultado', $resultado]],
                'resumen' => "$cuenta = $resultado",
            ];
    }
    throw new InvalidArgumentException('Punto desconocido.');
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new InvalidArgumentException('Envía los datos desde el formulario de cada punto.');
    }
    $respuesta = procesar();
} catch (InvalidArgumentException $e) {
    $respuesta = ['error' => $e->getMessage()];
} catch (Throwable $e) {
    http_response_code(500);
    $respuesta = ['error' => 'Ocurrió un error inesperado en el servidor.'];
}

echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
