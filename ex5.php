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

require_once 'iterators.php';
require_once 'commands.php';
require_once 'states.php';
require_once 'visitors.php';

abstract class LightNode {
    abstract public function outerHTML(): string;
    abstract public function innerHTML(): string;
    abstract public function accept(NodeVisitor $visitor): void;
    protected function onCreated(): void {}
    protected function onInserted(LightNode $parent): void {}
    protected function onBeforeRender(): void {}
}

class LightTextNode extends LightNode {
    private string $text;

    public function __construct(string $text) {
        $this->text = $text;
        $this->onCreated();
    }

    public function outerHTML(): string {
        $this->onBeforeRender();
        return $this->text;
    }

    public function innerHTML(): string {
        return $this->text;
    }

    public function accept(NodeVisitor $visitor): void {
        $visitor->visitText($this);
    }
}

class LightElementNode extends LightNode {
    private string $tagName;
    private string $displayType;
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
        $this->onCreated();
    }

    public function getTagName(): string { return $this->tagName; }
    public function getChildren(): array { return $this->children; }
    public function getCssClasses(): array { return $this->cssClasses; }

    public function addChild(LightNode $node): void {
        $this->children[] = $node;
        $node->onInserted($this);
    }

    public function addClass(string $className): void {
        if (!in_array($className, $this->cssClasses)) { $this->cssClasses[] = $className; }
    }

    public function removeClass(string $className): void {
        if (($key = array_search($className, $this->cssClasses)) !== false) { unset($this->cssClasses[$key]); }
    }

    public function hasClass(string $className): bool {
        return in_array($className, $this->cssClasses);
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
        $this->onBeforeRender();
        $classString = !empty($this->cssClasses) ? ' class="' . implode(' ', $this->cssClasses) . '"' : '';
        $openingTag = "<{$this->tagName}{$classString}";

        if ($this->isSelfClosing) {
            return $openingTag . " />";
        }

        return $openingTag . ">" . $this->innerHTML() . "</{$this->tagName}>";
    }

    public function accept(NodeVisitor $visitor): void {
        $visitor->visitElement($this);
        foreach ($this->children as $child) {
            $child->accept($visitor);
        }
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

    echo "Кількість рядків у таблиці: " . $table->getChildrenCount() . "\n\n";

    echo "=== innerHTML таблиці ===\n";
    echo $table->innerHTML() . "\n\n";

    echo "=== outerHTML всієї структури ===\n";
    echo $table->outerHTML() . "\n";
    echo $hr->outerHTML() . "\n";

    echo "\n=== ДЕМОНСТРАЦІЯ НОВИХ ШАБЛОНІВ ===\n\n";

    echo "--- Обхід дерева в глибину (DFS) ---\n";
    $dfsIterator = new DepthFirstIterator($table);
    foreach ($dfsIterator as $node) {
        if ($node instanceof LightElementNode) {
            echo "Елемент: <" . $node->getTagName() . ">\n";
        }
    }

    echo "\n--- Тестування Команди ---\n";
    $editor = new DocumentEditor();
    $command = new AddClassCommand($table, 'js-active-table');

    $editor->executeCommand($command);
    echo "Після команди: " . $table->outerHTML() . "\n";

    $editor->undo();
    echo "Після скасування (Undo): " . $table->outerHTML() . "\n";

    echo "\n--- Тестування Стейту (Prod Mode) ---\n";
    $document = new LightHtmlDocument($table);
    $document->setState(new ProdRenderState());
    echo $document->render() . "\n\n";

    echo "--- Тестування Відвідувача (SEO) ---\n";
    $seo = new SeoValidatorVisitor();
    $table->accept($seo);
    print_r($seo->getWarnings());
}

main();