<?php

interface NodeVisitor {
    public function visitElement(LightElementNode $node): void;
    public function visitText(LightTextNode $node): void;
}

class SeoValidatorVisitor implements NodeVisitor {
    private array $warnings = [];

    public function visitElement(LightElementNode $node): void {
        if ($node->getTagName() === 'table' && empty($node->getCssClasses())) {
            $this->warnings[] = "SEO Попередження: Знайдено таблицю <table> без CSS класів.";
        }
    }

    public function visitText(LightTextNode $node): void {
        if (strlen($node->innerHTML()) > 500) {
            $this->warnings[] = "SEO Попередження: Текстовий вузол занадто довгий.";
        }
    }

    public function getWarnings(): array { return $this->warnings; }
}