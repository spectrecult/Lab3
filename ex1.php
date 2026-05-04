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

class Logger {
    public function log(string $message): void {
        echo "\033[32m[LOG]: $message\033[0m\n";
    }

    public function error(string $message): void {
        echo "\033[31m[ERROR]: $message\033[0m\n";
    }

    public function warn(string $message): void {
        echo "\033[33m[WARN]: $message\033[0m\n";
    }
}

class FileWriter {
    private $filename;

    public function __construct(string $filename) {
        $this->filename = $filename;
    }

    public function write(string $text): void {
        file_put_contents($this->filename, $text, FILE_APPEND);
    }

    public function writeLine(string $text): void {
        file_put_contents($this->filename, $text . PHP_EOL, FILE_APPEND);
    }
}

class FileLoggerAdapter extends Logger {
    private $fileWriter;

    public function __construct(FileWriter $fileWriter) {
        $this->fileWriter = $fileWriter;
    }

    public function log(string $message): void {
        $this->fileWriter->writeLine("[LOG] " . date('Y-m-d H:i:s') . ": " . $message);
    }

    public function error(string $message): void {
        $this->fileWriter->writeLine("[ERROR] " . date('Y-m-d H:i:s') . ": " . $message);
    }

    public function warn(string $message): void {
        $this->fileWriter->writeLine("[WARN] " . date('Y-m-d H:i:s') . ": " . $message);
    }
}

function main() {
    echo "--- Робота з Logger (Консоль) ---\n";
    $consoleLogger = new Logger();
    $consoleLogger->log("Програма запущена успішно.");
    $consoleLogger->warn("Мало оперативної пам'яті.");
    $consoleLogger->error("Критична помилка бази даних!");

    echo "\n--- Робота з FileLoggerAdapter (Файл) ---\n";

    $logFile = "app_log.txt";
    if (file_exists($logFile)) unlink($logFile);

    $fileWriter = new FileWriter($logFile);
    $fileLogger = new FileLoggerAdapter($fileWriter);

    $fileLogger->log("Запис логу у файл.");
    $fileLogger->warn("Попередження збережено.");
    $fileLogger->error("Помилка записана у файл.");

    echo "Логи успішно записані у файл: $logFile\n";
    echo "Вміст файлу:\n";
    echo file_get_contents($logFile);
}

main();