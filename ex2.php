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

interface Hero {
    public function getDescription(): string;
    public function getStats(): int;
}


class Warrior implements Hero {
    public function getDescription(): string {
        return "Воїн";
    }
    public function getStats(): int {
        return 10; // Базова сила
    }
}

class Mage implements Hero {
    public function getDescription(): string {
        return "Маг";
    }
    public function getStats(): int {
        return 5; // Базова мана
    }
}

class Paladin implements Hero {
    public function getDescription(): string {
        return "Паладин";
    }
    public function getStats(): int {
        return 8;
    }
}

abstract class InventoryDecorator implements Hero {
    protected $hero;

    public function __construct(Hero $hero) {
        $this->hero = $hero;
    }

    public function getDescription(): string {
        return $this->hero->getDescription();
    }

    public function getStats(): int {
        return $this->hero->getStats();
    }
}

class SwordDecorator extends InventoryDecorator {
    public function getDescription(): string {
        return parent::getDescription() . " + Сталевий меч";
    }

    public function getStats(): int {
        return parent::getStats() + 15; // Меч додає 15 до статів
    }
}

class ArmorDecorator extends InventoryDecorator {
    public function getDescription(): string {
        return parent::getDescription() . " + Важка броня";
    }

    public function getStats(): int {
        return parent::getStats() + 20; // Броня додає 20 до захисту/статів
    }
}

class MagicArtifactDecorator extends InventoryDecorator {
    public function getDescription(): string {
        return parent::getDescription() . " + Магічний артефакт";
    }

    public function getStats(): int {
        return parent::getStats() + 50; // Артефакт дає великий бонус
    }
}

function main() {
    echo "=== Створення персонажів та екіпірування ===\n\n";

    $warrior = new Warrior();
    echo "1. " . $warrior->getDescription() . " (Стати: " . $warrior->getStats() . ")\n";

    $warriorWithSword = new SwordDecorator($warrior);
    echo "2. " . $warriorWithSword->getDescription() . " (Стати: " . $warriorWithSword->getStats() . ")\n";

    $fullyEquippedWarrior = new ArmorDecorator($warriorWithSword);
    echo "3. " . $fullyEquippedWarrior->getDescription() . " (Стати: " . $fullyEquippedWarrior->getStats() . ")\n";

    echo "-------------------------------------------\n";

    $mage = new Mage();
    $archMage = new MagicArtifactDecorator(new MagicArtifactDecorator($mage));

    echo "4. " . $archMage->getDescription() . " (Стати: " . $archMage->getStats() . ")\n";

    echo "-------------------------------------------\n";

    $paladin = new Paladin();
    $godLikePaladin = new MagicArtifactDecorator(
        new ArmorDecorator(
            new SwordDecorator($paladin)
        )
    );

    echo "5. " . $godLikePaladin->getDescription() . "\n";
    echo "   Фінальні характеристики: " . $godLikePaladin->getStats() . "\n";
}

// Запуск
main();