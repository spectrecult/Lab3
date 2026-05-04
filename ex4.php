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

interface TextReader {
    public function readFile(string $filename): ?array;
}

class SmartTextReader implements TextReader {
    public function readFile(string $filename): ?array {
        if (!file_exists($filename)) {
            echo "Файл '$filename' не знайдено.\n";
            return null;
        }

        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        $result = [];

        foreach ($lines as $line) {
            $result[] = mb_str_split($line);
        }

        return $result;
    }
}

class SmartTextChecker implements TextReader {
    private $reader;

    public function __construct(SmartTextReader $reader) {
        $this->reader = $reader;
    }

    public function readFile(string $filename): ?array {
        echo ">>> Спроба відкриття файлу: '$filename'...\n";

        $data = $this->reader->readFile($filename);

        if ($data !== null) {
            echo ">>> Файл успішно прочитано.\n";

            $rowCount = count($data);
            $charCount = 0;
            foreach ($data as $row) {
                $charCount += count($row);
            }

            echo ">>> Статистика: Рядків: $rowCount, Символів: $charCount.\n";
            echo ">>> Файл закрито.\n";
        }

        return $data;
    }
}

class SmartTextReaderLocker implements TextReader {
    private $reader;
    private $regex;

    public function __construct(TextReader $reader, string $pattern) {
        $this->reader = $reader;
        $this->regex = $pattern;
    }

    public function readFile(string $filename): ?array {
        if (preg_match($this->regex, $filename)) {
            echo "!!! Access denied! Доступ до файлу '$filename' заблоковано проксі-фільтром.\n";
            return null;
        }

        return $this->reader->readFile($filename);
    }
}

function main() {
    file_put_contents("public.txt", "Hello World\nPHP is cool");
    file_put_contents("secret_data.txt", "Top Secret Content");

    $realReader = new SmartTextReader();

    echo "--- ТЕСТ 1: Робота через SmartTextChecker (Логування) ---\n";
    $checkerProxy = new SmartTextChecker($realReader);
    $result1 = $checkerProxy->readFile("public.txt");

    echo "Вміст (перші 2 символи першого рядка): " . $result1[0][0] . $result1[0][1] . "...\n\n";

    echo "--- ТЕСТ 2: Робота через SmartTextReaderLocker (Захист) ---\n";
    $lockerProxy = new SmartTextReaderLocker($checkerProxy, "/secret/i");

    echo "Спроба прочитати секретний файл:\n";
    $lockerProxy->readFile("secret_data.txt");

    echo "\nСпроба прочитати звичайний файл через ланцюжок проксі:\n";
    $lockerProxy->readFile("public.txt");

    unlink("public.txt");
    unlink("secret_data.txt");
}

main();