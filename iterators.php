<?php

class DepthFirstIterator implements Iterator {
    private array $stack = [];
    private ?LightNode $current = null;
    private int $position = 0;

    public function __construct(LightNode $root) {
        $this->stack = [$root];
    }

    public function current(): mixed { return $this->current; }
    public function key(): mixed { return $this->position; }
    public function rewind(): void { $this->position = 0; }

    public function valid(): bool {
        return $this->current !== null || !empty($this->stack);
    }

    public function next(): void {
        if (empty($this->stack)) {
            $this->current = null;
            return;
        }

        $node = array_pop($this->stack);
        $this->current = $node;
        $this->position++;

        if ($node instanceof LightElementNode) {
            $children = array_reverse($node->getChildren());
            foreach ($children as $child) {
                $this->stack[] = $child;
            }
        }
    }
}

class BreadthFirstIterator implements Iterator {
    private array $queue = [];
    private ?LightNode $current = null;
    private int $position = 0;

    public function __construct(LightNode $root) {
        $this->queue = [$root];
    }

    public function current(): mixed { return $this->current; }
    public function key(): mixed { return $this->position; }
    public function rewind(): void { $this->position = 0; }

    public function valid(): bool {
        return $this->current !== null || !empty($this->queue);
    }

    public function next(): void {
        if (empty($this->queue)) {
            $this->current = null;
            return;
        }

        $node = array_shift($this->queue);
        $this->current = $node;
        $this->position++;

        if ($node instanceof LightElementNode) {
            foreach ($node->getChildren() as $child) {
                $this->queue[] = $child;
            }
        }
    }
}