<?php
/**
 * Task.php
 * 
 * Classe Task per gestionar les tasques de l'aplicació.
 * Permet crear, actualitzar i carregar tasques des de la base de dades.
 * Utilitza PDO per a la connexió a la BBDD i JSON per emmagatzemar etiquetes.
 */

class Task
{
    /**
     * @var PDO $pdo
     * Instància de PDO per interactuar amb la base de dades
     */
    private $pdo;
    
    // Propietats de la tasca
    public $id;
    public $title;
    public $description = '';
    public $tags = []; // Array d'etiquetes
    public $cost = 0;
    public $due_date;
    public $expected_hours = 20;
    public $used_hours = 0;
    public $priority = 'medium';
    public $state = 'pending';
    public $finished_at;
    public $created_at;
    public $updated_at;

    /**
     * Constructor
     * Inicialitza la tasca amb la connexió PDO i valors per defecte
     * @param PDO $pdo Connexió PDO a la base de dades
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        // Per defecte, el due_date és 24 hores més tard
        $this->due_date = date('Y-m-d H:i:s', time() + 24 * 60 * 60);

        // Inicialitzar timestamps
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    /**
     * Guardar nova tasca a la base de dades
     * Assigna automàticament l'id generat
     */
    public function save(): bool
    {
        try {
            $sql = "INSERT INTO tasks (
                        title, description, tags, cost, due_date, expected_hours, used_hours,
                        image_url, image_local_name, image_cloud_public_id,
                        priority, state, finished_at, created_at, updated_at
                    ) VALUES (
                        :title, :description, :tags, :cost, :due_date, :expected_hours, :used_hours,
                        :image_url, :image_local_name, :image_cloud_public_id,
                        :priority, :state, :finished_at, :created_at, :updated_at
                    )";
    
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':title' => $this->title,
                ':description' => $this->description,
                ':tags' => json_encode($this->tags),
                ':cost' => $this->cost,
                ':due_date' => $this->due_date,
                ':expected_hours' => $this->expected_hours,
                ':used_hours' => $this->used_hours,
                ':priority' => $this->priority,
                ':state' => $this->state,
                ':finished_at' => $this->finished_at,
                ':created_at' => $this->created_at,
                ':updated_at' => $this->updated_at,
            ]);
            
            if (!$ok) {
                return false;
            }

            // Assignem l'id generat per la BBDD
            $this->id = (int)$this->pdo->lastInsertId();
            return true;
        } 
        catch (PDOException $e) 
        {
            throw new RuntimeException(
                'No s\'ha pogut guardar correctament la tasca...',
                0,
                $e
            );
        }
    }

    /**
     * @param int $id - ID de la tasca a carregar
     * Carregar una tasca existent per ID
     * Executa metode privat fillFromArray per convertir les dades a objecte
     */
    public function load(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!data) {
            return false;
        }

        $this->fillFromArray($data);
        return true;
    }

    /**
     * @param array $data - array associatiu
     * S'encarrega de retornar aquest array associatiu en un objecte
     */
    private function fillFromArray(array $data): void
    {
        $this->id = $data['id'];
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->tags = json_decode($data['tags'], true) ?: [];
        $this->cost = $data['cost'];
        $this->due_date = $data['due_date'];
        $this->expected_hours = $data['expected_hours'];
        $this->used_hours = $data['used_hours'];
        $this->priority = $data['priority'];
        $this->state = $data['state'];
        $this->finished_at = $data['finished_at'];
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'];
    }

    /**
     * Actualitzar una tasca existent
     * Assigna updated_at i, si es marca com a completada, també finished_at
     */
    public function update(): bool
    {

        if (!$this->id) {
            throw new LogicException('No es pot actualitzar una tasca sense ID!');
        }

        $this->updated_at = date('Y-m-d H:i:s');

        // Si s'ha marcat com a completada i no té data de finalització
        if ($this->state === 'completed' && !$this->finished_at) {
            $this->finished_at = $this->updated_at;
        }

        try {
            $stmt = $this->pdo->prepare(
                "UPDATE tasks SET
                    title = :title,
                    description = :description,
                    tags = :tags,
                    cost = :cost,
                    due_date = :due_date,
                    expected_hours = :expected_hours,
                    used_hours = :used_hours,
                    priority = :priority,
                    state = :state,
                    finished_at = :finished_at,
                    updated_at = :updated_at
                WHERE id = :id"  
            );
            // Executem l'actualització amb els valors de la instància
            return $stmt->execute([
                ':title' => $this->title,
                ':description' => $this->description,
                ':tags' => json_encode($this->tags),
                ':cost' => $this->cost,
                ':due_date' => $this->due_date,
                ':expected_hours' => $this->expected_hours,
                ':used_hours' => $this->used_hours,
                ':priority' => $this->priority,
                ':state' => $this->state,
                ':finished_at' => $this->finished_at,
                ':updated_at' => $this->updated_at,
                ':id' => $this->id,
            ]);
        }
        catch (PDOException $e)
        {
            throw new RuntimeException(
                'Hi ha hagut un error al actualitzar la tasca.',
                0,
                $e
            );
        }
    }

    /**
     * Assegura que la tasca desitjada sigui eliminada correctament.
     */
    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }

        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute([ ':id' => $this->id ]);
    }

    /**
     * Retorna totes les tasques emmagatzemades a la base de dades.
     *
     * Executa una consulta SELECT i transforma cada fila (array associatiu)
     * retornada per fetchAll() en un objecte Task mitjançant fillFromArray().
     *
     * @param PDO $pdo Connexió a la base de dades
     * @return Task[] Array d'objectes Task
     */
    public static function findAll(PDO $pdo): array
    {
        $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tasks = [];
        foreach ( $rows as $row ) {
            $task = new self($pdo);
            $task->fillFromArray($row);
            $tasks[] = $task;
        }

        return $tasks;
    }
}