<?php

namespace Tests\Framework\Database;

use Framework\Database\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    private $table;
    public function setUp(): void
    {
        $dsn = 'mysql:host=localhost;dbname=grafiblog';
        $user = 'root';
        $password = '';
        $pdo = new \PDO($dsn, $user, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
        ]);

        //$pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY AUTO_INCREMENT, name VARCHAR(250))');
        $this->table = new Table($pdo);
        $reflection = new \ReflectionClass($this->table);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        $property->setValue($this->table, 'test');
    }

    public function testFind()
    {
        $this->table->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->table->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $test = $this->table->find(1);
        $this->assertInstanceOf(\stdClass::class, $test);
        $this->assertEquals('a1', $test->name);
    }

    public function testFindList()
    {
        $this->table->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->table->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $test = $this->table->findList();
        $this->assertEquals(['1' => 'a1', '2' => 'a2'], $test);
    }
}
