<style>
    body {
        background-color: #f4f4f9;
        font-family: 'Courier New', monospace;
        white-space: pre-wrap;
        line-height: 1.5;
        padding: 20px;
        color: #333;
    }
</style>

<?php

interface Renderer {
    public function renderShape(string $shapeName): void;
}

class RasterRenderer implements Renderer {
    public function renderShape(string $shapeName): void {
        echo "Drawing $shapeName as pixels (Raster Graphics)\n";
    }
}

class VectorRenderer implements Renderer {
    public function renderShape(string $shapeName): void {
        echo "Drawing $shapeName as lines (Vector Graphics)\n";
    }
}

abstract class Shape {
    protected $renderer;

    public function __construct(Renderer $renderer) {
        $this->renderer = $renderer;
    }

    abstract public function draw(): void;
}

class Circle extends Shape {
    public function draw(): void {
        $this->renderer->renderShape("Circle");
    }
}

class Square extends Shape {
    public function draw(): void {
        $this->renderer->renderShape("Square");
    }
}

class Triangle extends Shape {
    public function draw(): void {
        $this->renderer->renderShape("Triangle");
    }
}

function main() {
    // Ств типи рендерингу
    $raster = new RasterRenderer();
    $vector = new VectorRenderer();

    echo "--- Робота графічного редактора ---\n\n";

    // Мал фігури у векторному форматі
    $vectorCircle = new Circle($vector);
    $vectorTriangle = new Triangle($vector);

    echo "Векторний режим:\n";
    $vectorCircle->draw();
    $vectorTriangle->draw();

    echo "\n" . str_repeat("-", 35) . "\n\n";

    // Мал фігури у растровому форматі
    $rasterSquare = new Square($raster);
    $rasterTriangle = new Triangle($raster); // Той самий тип фігури, інший рендер

    echo "Растровий режим:\n";
    $rasterSquare->draw();
    $rasterTriangle->draw();
}

// Запуск
main();