<?php

interface RenderState {
    public function renderNode(LightNode $node): string;
}

class DevRenderState implements RenderState {
    public function renderNode(LightNode $node): string {
        return "<!-- START NODE -->\n" . $node->outerHTML() . "\n<!-- END NODE -->\n";
    }
}

class ProdRenderState implements RenderState {
    public function renderNode(LightNode $node): string {
        return preg_replace('/\s+/', ' ', $node->outerHTML());
    }
}

class LightHtmlDocument {
    private LightNode $root;
    private RenderState $state;

    public function __construct(LightNode $root) {
        $this->root = $root;
        $this->state = new DevRenderState();
    }

    public function setState(RenderState $state): void {
        $this->state = $state;
    }

    public function render(): string {
        return $this->state->renderNode($this->root);
    }
}