<?php

namespace Tests\Database;

!defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 2));

include_once BASE_PATH . '/vendor/autoload.php';

use Minichan\Database\Database;
use PHPUnit\Framework\TestCase;

class DBTestCase extends TestCase
{
    protected Database $database;

    protected function setUp(): void
    {
        $this->database = new Database(['testMode' => true]);
    }

    public static function typesProvider(): array
    {
        return [
            'MySQL' => ['mysql'],
            'MSSQL' => ['mssql'],
            'SQLite' => ['sqlite'],
            'PostgreSQL' => ['pgsql'],
            'Oracle' => ['oracle'],
        ];
    }

    protected function setType(string $type): void
    {
        $this->database->type = $type;
    }

    protected function expectedQuery(string $expected): string
    {
        $identifier = [
            'mysql' => '`$1`',
            'mssql' => '[$1]',
        ];
    
        $normalizedExpected = preg_replace('/\s+/', ' ', $expected);
    
        return preg_replace_callback(
            '/(?!\'[^\s]+\s?)"([\p{L}_][\p{L}\p{N}@$#\-_]*)"(?!\s?[^\s]+\')/u',
            function ($matches) use ($identifier) {
                return $identifier[$this->database->type] ?? '"'.$matches[1].'"';
            },
            $normalizedExpected
        );
    }
    

    protected function assertQuery($expected, $query): void
    {
        if (is_array($expected)) {
            $this->assertEquals(
                $this->expectedQuery($expected[$this->database->type] ?? $expected['default']),
                $query
            );
        } else {
            $this->assertEquals($this->expectedQuery($expected), $query);
        }
    }
}

class Foo
{
    public $bar = "cat";

    public function __wakeup()
    {
        $this->bar = "dog";
    }
}