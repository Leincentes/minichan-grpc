<?php

declare(strict_types=1);
namespace Tests\Database;

use Minichan\Database\Database;

class DeleteTest extends \Tests\Database\DBTestCase
{
    /**
     * @covers ::delete()
     * @dataProvider typesProvider
     */
    public function testDelete($type)
    {
        $this->setType($type);

        $this->database->delete("account", [
            "AND" => [
                "type" => "business",
                "age[<]" => 18
            ]
        ]);

        $this->assertQuery(
            <<<EOD
            DELETE FROM "account"
            WHERE ("type" = 'business' AND "age" < 18)
            EOD,
            $this->database->queryString
        );
    }

    /**
     * @covers ::delete()
     * @dataProvider typesProvider
     */
    public function testDeleteRaw($type)
    {
        $this->setType($type);

        $whereClause = Database::raw("WHERE (<type> = :type AND <age> < :age)", [
            ':type' => 'business',
            ':age' => 18,
        ]);

        $this->database->delete("account", $whereClause);

        $this->assertQuery(
            <<<EOD
            DELETE FROM "account"
            WHERE ("type" = 'business' AND "age" < 18)
            EOD,
            $this->database->queryString
        );
    }
}
