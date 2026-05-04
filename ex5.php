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

abstract class LightNode {
    abstract public function outerHTML(): string;
    abstract public function innerHTML(): string;
}

class LightTextNode extends LightNode {
    private string $text;

    public function __construct(string $text) {
        $this->text = $text;
    }

    public function outerHTML(): string {
        return $this->text;
    }

    public function innerHTML(): string {
        return $this->text;
    }
}

class LightElementNode extends LightNode {
    private string $tagName;
    private string $displayType; // block або inline
    private bool $isSelfClosing;
    private array $cssClasses = [];
    private array $children = [];

    public function __construct(
        string $tagName,
        string $displayType = 'block',
        bool $isSelfClosing = false,
        array $cssClasses = []
    ) {
        $this->tagName = $tagName;
        $this->displayType = $displayType;
        $this->isSelfClosing = $isSelfClosing;
        $this->cssClasses = $cssClasses;
    }

    public function addChild(LightNode $node): void {
        $this->children[] = $node;
    }

    public function getChildrenCount(): int {
        return count($this->children);
    }

    public function innerHTML(): string {
        $html = "";
        foreach ($this->children as $child) {
            $html .= $child->outerHTML();
        }
        return $html;
    }

    public function outerHTML(): string {
        $classString = !empty($this->cssClasses) ? ' class="' . implode(' ', $this->cssClasses) . '"' : '';
        $openingTag = "<{$this->tagName}{$classString}";

        if ($this->isSelfClosing) {
            return $openingTag . " />";
        }

        return $openingTag . ">" . $this->innerHTML() . "</{$this->tagName}>";
    }
}
function main() {
    echo "--- Створення структури LightHTML (Таблиця) ---\n\n";

    $table = new LightElementNode('table', 'block', false, ['my-table', 'striped']);

    $headerRow = new LightElementNode('tr', 'block');

    $th1 = new LightElementNode('th', 'inline');
    $th1->addChild(new LightTextNode("ID"));

    $th2 = new LightElementNode('th', 'inline');
    $th2->addChild(new LightTextNode("Назва продукту"));

    $headerRow->addChild($th1);
    $headerRow->addChild($th2);

    $dataRow = new LightElementNode('tr', 'block');

    $td1 = new LightElementNode('td', 'inline');
    $td1->addChild(new LightTextNode("1"));

    $td2 = new LightElementNode('td', 'inline');
    $td2->addChild(new LightTextNode("Клавіатура ASUS TUF"));

    $dataRow->addChild($td1);
    $dataRow->addChild($td2);

    $hr = new LightElementNode('hr', 'block', true, ['divider']);

    $table->addChild($headerRow);
    $table->addChild($dataRow);

    // Вивід результатів
    echo "Кількість рядків у таблиці: " . $table->getChildrenCount() . "\n\n";

    echo "=== innerHTML таблиці ===\n";
    echo $table->innerHTML() . "\n\n";

    echo "=== outerHTML всієї структури ===\n";
    echo $table->outerHTML() . "\n";
    echo $hr->outerHTML() . "\n";
}

main();