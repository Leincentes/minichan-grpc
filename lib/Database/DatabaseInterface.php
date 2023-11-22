<?php
declare(strict_types=1);
namespace Minichan\Database;

interface DatabaseInterface
{
    /**
     * Perform a SELECT query on the database.
     *
     * @param string $table
     * @param array $columns
     * @param array|callable|null $whereOrCallback
     * @param callable|null $callback
     * @return array|null
     */
    public function select(string $table, array $columns, $whereOrCallback = null, ?callable $callback = null): ?array;

    /**
     * Perform a GET query on the database.
     *
     * @param string $table
     * @param array|string $columns
     * @param array $where
     * @return mixed
     */
    public function get(string $table, $columns, array $where);

    /**
     * Check if records exist in the database based on the provided conditions.
     *
     * @param string $table
     * @param array $where
     * @return bool
     */
    public function has(string $table, array $where): bool;

    /**
     * Retrieve a random record from the database based on the provided conditions.
     *
     * @param string $table
     * @param array|string $column
     * @param array $where
     * @return mixed
     */
    public function rand(string $table, $column, array $where);

    /**
     * Count the number of records in the database based on the provided conditions.
     *
     * @param string $table
     * @param array $where
     * @return int
     */
    public function count(string $table, array $where): ?int;

    /**
     * Retrieve the maximum value of a column from the database.
     *
     * @param string $table
     * @param string $column
     * @param array|null $where
     * @return string
     */
    public function max(string $table, string $column, ?array $where = null): ?string;

    /**
     * Retrieve the minimum value of a column from the database.
     *
     * @param string $table
     * @param string $column
     * @param array|null $where
     * @return string
     */
    public function min(string $table, string $column, ?array $where = null): ?string;

    /**
     * Retrieve the average value of a column from the database.
     *
     * @param string $table
     * @param string $column
     * @param array|null $where
     * @return string
     */
    public function avg(string $table, string $column, ?array $where = null): ?string;

    /**
     * Retrieve the sum of a column's values from the database.
     *
     * @param string $table
     * @param string $column
     * @param array|null $where
     * @return string
     */
    public function sum(string $table, string $column, ?array $where = null): ?string;
}
