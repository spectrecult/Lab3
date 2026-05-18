<?php

interface Command {
    public function execute(): void;
    public function undo(): void;
}

class AddClassCommand implements Command {
    private LightElementNode $node;
    private string $className;
    private bool $hadClass;

    public function __construct(LightElementNode $node, string $className) {
        $this->node = $node;
        $this->className = $className;
    }

    public function execute(): void {
        $this->hadClass = $this->node->hasClass($this->className);
        if (!$this->hadClass) { $this->node->addClass($this->className); }
    }

    public function undo(): void {
        if (!$this->hadClass) { $this->node->removeClass($this->className); }
    }
}

class DocumentEditor {
    private array $history = [];

    public function executeCommand(Command $command): void {
        $command->execute();
        $this->history[] = $command;
    }

    public function undo(): void {
        if (!empty($this->history)) {
            $command = array_pop($this->history);
            $command->undo();
        }
    }
}