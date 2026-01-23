<?php

/**
 * Classe Database
 *
 * Gestiona la connexió a la base de dades utilitzant PDO.
 * Implementa el patró Singleton per assegurar que només hi hagi una instància activa.
 * Inclou la comprovació automàtica de la base de dades i les taules necessàries.
 */
class Database
{
    /**
     * Instància Singleton de Database
     * @var Database|null
     */
    private static ?Database $instance = null;

    /**
     * Instància PDO per a la connexió
     * @var PDO
     */
    private PDO $pdo;

    private string $host;
    private string $dbName;
    private string $user;
    private string $pass;

    /**
     * Constructor privat (Singleton)
     *
     * @param string $host Nom del servidor
     * @param string $dbName Nom de la base de dades
     * @param string $user Usuari de la base de dades
     * @param string $pass Contrasenya de la base de dades
     */
    private function __construct(string $host, string $dbName, string $user, string $pass)
    {
        $this->host   = $host;
        $this->dbName = $dbName;
        $this->user   = $user;
        $this->pass   = $pass;

        // Connexió inicial sense especificar base de dades (necessari per crear-la si no existeix)
        $this->connect();

        // Comprova i crea la base de dades si cal
        $this->checkDatabase();

        // Comprova i crea les taules si cal
        $this->checkTables();
    }

    /**
     * Retorna la instància Singleton de Database
     *
     * @param string|null $host
     * @param string|null $dbName
     * @param string|null $user
     * @param string|null $pass
     * @return Database
     */
    public static function getInstance(
        string $host = null,
        string $dbName = null,
        string $user = null,
        string $pass = null
    ): Database {
        if (!self::$instance) {
            // Si no es passen paràmetres, intentar llegir de les variables d'entorn
            $host   = $host ?? $_ENV['DB_HOST'];
            $dbName = $dbName ?? $_ENV['DB_NAME'];
            $user   = $user ?? $_ENV['DB_USER'];
            $pass   = $pass ?? $_ENV['DB_PASS'];

            // Crear la instància Singleton
            self::$instance = new self($host, $dbName, $user, $pass);
        }

        return self::$instance;
    }

    /**
     * Estableix la connexió PDO inicial
     *
     * Aquesta connexió no selecciona cap base de dades encara,
     * per permetre crear-la si no existeix.
     *
     * @return void
     * @throws RuntimeException
     */
    private function connect(): void
    {
        try {
            $this->pdo = new PDO("mysql:host={$this->host}", $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'No s\'ha pogut connectar al servidor de bases de dades.',
                0,
                $e
            );
        }
    }

    /**
     * Comprova si la base de dades existeix i la crea si cal
     *
     * També selecciona la base de dades per a futures consultes.
     *
     * @return void
     * @throws RuntimeException
     */
    private function checkDatabase(): void
    {
        try {
            $sql = file_get_contents(APP_ROOT . '/config/dbBasicFiles/create_database.sql');
            $this->pdo->exec($sql);
            $this->pdo->exec("USE `{$this->dbName}`");
        } catch (PDOException $e) {
            throw new RuntimeException(
                'No s\'ha pogut crear o utilitzar la base de dades correctament.',
                0,
                $e
            );
        }
    }

    /**
     * Comprova si les taules necessàries existeixen i les crea si cal
     *
     * @return void
     * @throws RuntimeException
     */
    private function checkTables(): void
    {
        try {
            $sql = file_get_contents(APP_ROOT . '/config/dbBasicFiles/create_tables.sql');
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'No s\'han pogut crear les taules correctament.',
                0,
                $e
            );
        }
    }

    /**
     * Retorna la connexió PDO
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}