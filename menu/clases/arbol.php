<?php
require_once __DIR__ . '/nodo.php';

class Arbol
{

    public function construir(array $pre, array $in, array $post): Nodo
    {
        $dados = array_filter([$pre, $in, $post]);
        if (count($dados) < 2) {
            throw new InvalidArgumentException('Ingresa al menos dos recorridos.');
        }

        $primero = reset($dados);
        foreach ($dados as $recorrido) {
            if (count($recorrido) !== count(array_unique($recorrido))) {
                throw new InvalidArgumentException('Los nodos no pueden repetirse.');
            }
            if (count($recorrido) !== count($primero) || array_diff($recorrido, $primero)) {
                throw new InvalidArgumentException('Los recorridos deben tener los mismos nodos.');
            }
        }

        if ($pre && $in) {
            $raiz = $this->preIn($pre, $in);
        } elseif ($post && $in) {
            $raiz = $this->postIn($post, $in);
        } else {
            $raiz = $this->prePost($pre, $post);
        }

        if (($pre && $this->preorden($raiz) !== $pre)
            || ($in && $this->inorden($raiz) !== $in)
            || ($post && $this->postorden($raiz) !== $post)) {
            throw new InvalidArgumentException('Los recorridos no corresponden al mismo árbol.');
        }
        return $raiz;
    }

    public function preorden(?Nodo $n): array
    {
        return $n ? array_merge([$n->valor], $this->preorden($n->izq), $this->preorden($n->der)) : [];
    }

    public function inorden(?Nodo $n): array
    {
        return $n ? array_merge($this->inorden($n->izq), [$n->valor], $this->inorden($n->der)) : [];
    }

    public function postorden(?Nodo $n): array
    {
        return $n ? array_merge($this->postorden($n->izq), $this->postorden($n->der), [$n->valor]) : [];
    }

    private function posicion(string $valor, array $lista): int
    {
        $i = array_search($valor, $lista, true);
        if ($i === false) {
            throw new InvalidArgumentException('Los recorridos no corresponden al mismo árbol.');
        }
        return $i;
    }

    private function preIn(array $pre, array $in): ?Nodo
    {
        if (!$pre) {
            return null;
        }
        $nodo = new Nodo($pre[0]);
        $i = $this->posicion($pre[0], $in);
        $nodo->izq = $this->preIn(array_slice($pre, 1, $i), array_slice($in, 0, $i));
        $nodo->der = $this->preIn(array_slice($pre, $i + 1), array_slice($in, $i + 1));
        return $nodo;
    }

    private function postIn(array $post, array $in): ?Nodo
    {
        if (!$post) {
            return null;
        }
        $raiz = $post[count($post) - 1];
        $nodo = new Nodo($raiz);
        $i = $this->posicion($raiz, $in);
        $nodo->izq = $this->postIn(array_slice($post, 0, $i), array_slice($in, 0, $i));
        $nodo->der = $this->postIn(array_slice($post, $i, count($post) - $i - 1), array_slice($in, $i + 1));
        return $nodo;
    }

    private function prePost(array $pre, array $post): ?Nodo
    {
        if (!$pre) {
            return null;
        }
        $nodo = new Nodo($pre[0]);
        if (count($pre) > 1) {
            $n = $this->posicion($pre[1], $post) + 1;
            $nodo->izq = $this->prePost(array_slice($pre, 1, $n), array_slice($post, 0, $n));
            $nodo->der = $this->prePost(array_slice($pre, 1 + $n), array_slice($post, $n, -1));
        }
        return $nodo;
    }
}
