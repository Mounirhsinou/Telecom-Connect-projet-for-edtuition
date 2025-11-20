<?php
/**
 * Database Connection Handler
 * 
 * Provides a secure PDO database connection with error handling.
 * Uses singleton pattern to ensure only one connection exists.
 * 
 * @package TelecomWebsite
 * @version 1.0.0
 */

// Load configuration
require_once __DIR__ . '/../config.php';

/**
 * Database class
 * Handles database connections using PDO with prepared statements
 */
class Database
{

    /** @var PDO|null Database connection instance */
    private static $instance = null;

    /** @var PDO Database connection object */
    private $connection;

    /**
     * Private constructor to prevent direct instantiation
     * Implements singleton pattern
     */
    private function __construct()
    {
        try {
            // Build DSN (Data Source Name)
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            // PDO options for security and performance
            $options = [
                // Use real prepared statements (not emulated)
                PDO::ATTR_EMULATE_PREPARES => false,

                // Throw exceptions on errors
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                // Fetch associative arrays by default
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                // Disable persistent connections for better security
                PDO::ATTR_PERSISTENT => false,

                // Set connection timeout
                PDO::ATTR_TIMEOUT => 5,
            ];

            // Create PDO connection
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);

            // Log successful connection in debug mode
            if (DEBUG_MODE) {
                error_log('Database connection established successfully');
            }

        } catch (PDOException $e) {
            // Log error
            error_log('Database connection failed: ' . $e->getMessage());

            // Display user-friendly error
            if (DISPLAY_ERRORS) {
                die('Database connection error: ' . $e->getMessage());
            } else {
                die('Database connection error. Please contact the administrator.');
            }
        }
    }

    /**
     * Get database instance (singleton)
     * 
     * @return Database Database instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection object
     * 
     * @return PDO PDO connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * Execute a SELECT query with prepared statements
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return array Result set
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Query error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw $e;
        }
    }

    /**
     * Execute a single row SELECT query
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return array|false Single row or false if not found
     */
    public function queryOne($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Query error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw $e;
        }
    }

    /**
     * Execute INSERT, UPDATE, DELETE queries
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return bool Success status
     */
    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Execute error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw $e;
        }
    }

    /**
     * Get last inserted ID
     * 
     * @return string Last insert ID
     */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Begin transaction
     * 
     * @return bool Success status
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     * 
     * @return bool Success status
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Rollback transaction
     * 
     * @return bool Success status
     */
    public function rollback()
    {
        return $this->connection->rollBack();
    }

    /**
     * Check if currently in a transaction
     * 
     * @return bool Transaction status
     */
    public function inTransaction()
    {
        return $this->connection->inTransaction();
    }

    /**
     * Escape string for LIKE queries
     * 
     * @param string $string String to escape
     * @return string Escaped string
     */
    public function escapeLike($string)
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $string);
    }
}

/**
 * Helper function to get database instance
 * 
 * @return PDO PDO connection object
 */
function getDB()
{
    return Database::getInstance()->getConnection();
}

/**
 * Helper function to execute a query
 * 
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array Result set
 */
function dbQuery($sql, $params = [])
{
    return Database::getInstance()->query($sql, $params);
}

/**
 * Helper function to execute a single row query
 * 
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array|false Single row or false
 */
function dbQueryOne($sql, $params = [])
{
    return Database::getInstance()->queryOne($sql, $params);
}

/**
 * Helper function to execute INSERT/UPDATE/DELETE
 * 
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return bool Success status
 */
function dbExecute($sql, $params = [])
{
    return Database::getInstance()->execute($sql, $params);
}

/**
 * Helper function to get last insert ID
 * 
 * @return string Last insert ID
 */
function dbLastInsertId()
{
    return Database::getInstance()->lastInsertId();
}
