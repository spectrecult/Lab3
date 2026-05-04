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

class ElementType {
    public string $tagName;
    public string $displayType;
    public bool $isSelfClosing;

    public function __construct(string $tagName, string $displayType, bool $isSelfClosing) {
        $this->tagName = $tagName;
        $this->displayType = $displayType;
        $this->isSelfClosing = $isSelfClosing;
    }
}

class ElementFactory {
    private static array $types = [];

    public static function getType(string $tagName, string $displayType, bool $isSelfClosing): ElementType {
        $key = "{$tagName}_{$displayType}_" . ($isSelfClosing ? '1' : '0');
        if (!isset(self::$types[$key])) {
            self::$types[$key] = new ElementType($tagName, $displayType, $isSelfClosing);
        }
        return self::$types[$key];
    }

    public static function getTypesCount(): int {
        return count(self::$types);
    }
}

abstract class LightNode {
    abstract public function outerHTML(): string;
}

class LightTextNode extends LightNode {
    private string $text;
    public function __construct(string $text) { $this->text = $text; }
    public function outerHTML(): string { return htmlspecialchars($this->text); }
}

class LightElementNode extends LightNode {
    private ElementType $type; // Посилання на спільний стан
    private array $children = [];

    public function __construct(ElementType $type) {
        $this->type = $type;
    }

    public function addChild(LightNode $node): void {
        $this->children[] = $node;
    }

    public function outerHTML(): string {
        $html = "<{$this->type->tagName}>";
        foreach ($this->children as $child) {
            $html .= $child->outerHTML();
        }
        return $html . "</{$this->type->tagName}>\n";
    }
}

function main() {
    // Емуляція завантаження тексту книги (перші кілька абзаців для демонстрації)
    $bookText = "The Romeo and Juliet\nBY WILLIAM SHAKESPEARE\n  Soft you! a word or two before you go.\nStandard paragraph text for testing.";
    $lines = explode("\n", $bookText);

    $root = new LightElementNode(ElementFactory::getType('div', 'block', false));

    $startMemory = memory_get_usage();

    foreach ($lines as $index => $line) {
        $trimmedLine = trim($line);
        if (empty($trimmedLine)) continue;

        if ($index === 0) {
            $type = ElementFactory::getType('h1', 'block', false);
        } elseif (strlen($trimmedLine) < 20) {
            $type = ElementFactory::getType('h2', 'block', false);
        } elseif (str_starts_with($line, ' ')) {
            $type = ElementFactory::getType('blockquote', 'block', false);
        } else {
            $type = ElementFactory::getType('p', 'block', false);
        }

        $element = new LightElementNode($type);
        $element->addChild(new LightTextNode($trimmedLine));
        $root->addChild($element);
    }

    $endMemory = memory_get_usage();

    echo "=== Результат конвертації ===\n";
    echo $root->outerHTML();

    echo "\n=== Статистика пам'яті ===\n";
    echo "Використано пам'яті для дерева: " . ($endMemory - $startMemory) . " байт\n";
    echo "Кількість унікальних об'єктів ElementType (Легковаговиків): " . ElementFactory::getTypesCount() . "\n";
}

main();