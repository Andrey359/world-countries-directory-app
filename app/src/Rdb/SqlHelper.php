<?php

namespace App\Rdb;

use mysqli;
use Exception;

class SqlHelper
{
    private string $host;
    private string $username;
    private string $password;
    private string $database;
    private int $port;

    /**
     * Constructor initializes database connection parameters and tests connection
     * @param string $host Database host
     * @param string $username Database username
     * @param string $password Database password
     * @param string $database Database name
     * @param int $port Database port
     * @throws Exception If connection fails
     */
    public function __construct(
        string $host,
        string $username,
        string $password,
        string $database,
        int $port = 3306
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
        
        $this->pingDb();
    }

    /**
     * Open database connection
     * Creates and returns mysqli connection object
     * @return mysqli Database connection object
     * @throws Exception If connection fails
     */
    public function openDbConnection(): mysqli
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try {
            $connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database,
                $this->port
            );
            
            $connection->set_charset('utf8mb4');
            
            return $connection;
        } catch (\mysqli_sql_exception $e) {
            throw new Exception('Database connection error: ' . $e->getMessage());
        }
    }

    /**
     * Ping database to check availability
     * Tests connection and closes it immediately
     * @return void
     * @throws Exception If database is unavailable
     */
    private function pingDb(): void
    {
        try {
            $connection = $this->openDbConnection();
            
            if (!$connection->ping()) {
                throw new Exception('Database ping failed');
            }
            
            $connection->close();
        } catch (Exception $e) {
            throw new Exception('Database availability check failed: ' . $e->getMessage());
        }
    }
}
