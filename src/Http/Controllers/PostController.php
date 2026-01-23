<?php
use Psr\Log\LoggerInterface;

require_once APP_ROOT . '/models/Tasca.php';
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/helpers/Sanitizer.php';
require_once APP_ROOT . '/helpers/Validator.php';

/**
 * Controlador de tasques
 *
 * Gestiona CRUD de tasques utilitzant els helpers Sanitizer i Validator.
 */
class TaskController
{
    private PDO $pdo;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        // Obtenim la connexió PDO des del singleton Database
        $this->pdo = Database::getInstance()->getConnection();
        $this->logger = $logger;
    }

    /**
     * Llista totes les tasques
     */
    public function index(): void
    {
        $tasks = Task::findAll($this->pdo);

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/tasques/index.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * Mostra el formulari de creació
     */
    public function create(): void
    {
        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/tasques/create.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * Desa una nova tasca
     */
    public function store(): void
    {
        $data = Sanitizer::clean($_POST);

        // Validacions
        $validator = new Validator($data);
        $validator->required('title')->string('title', 3, 255);
        $validator->string('description', 0, 2000);
        $validator->tags('tags');
        $validator->numeric('cost');
        $validator->date('due_date');
        $validator->numeric('expected_hours');
        $validator->numeric('used_hours');
        $validator->enum('priority');
        $validator->enum('state');

        $this->logger->info('Petició de creació de tasca', [
            'post'         => $_POST,
            'title'        => $data['title'],
            'description'  => substr($data['description'], 0, 50),
            'priority'     => $data['priority']
        ]);

        if (!$validator->isValid()) {
            $errors = $validator->errors();
            // Tornem al formulari amb errors
            require APP_ROOT . '/views/tasques/create.php';
            return;
        }

        // Creem la tasca
        $task                 = new Task($this->pdo);
        $task->title          = $data['title'];
        $task->description    = $data['description'];
        $task->tags           = $data['tags'];
        $task->cost           = $data['cost'];
        $task->due_date       = $data['due_date'];
        $task->expected_hours = $data['expected_hours'];
        $task->used_hours     = $data['used_hours'];
        $task->priority       = $data['priority'];
        $task->state          = $data['state'];

        $task->save();

        $this->logger->info('Tasca creada', [
            'title'       => $task->title,
            'description' => substr($task->description, 0, 50),
            'priority'    => $task->priority
        ]);

        header('Location:' . BASE_PATH . '/tasques');
        exit;
    }

    /**
     * Mostra el formulari d'edició d'una tasca
     */
    public function edit(int $id): void
    {
        $task = new Task($this->pdo);

        if (!$task->load($id)) {
            http_response_code(404);
            require APP_ROOT . '/../views/home/404.php';
            return;
        }
        
        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/tasques/edit.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * Actualitza una tasca existent
     */
    public function update(int $id): void
    {
        $task = new Task($this->pdo);

        if (!$task->load($id)) {
            http_response_code(404);
            require APP_ROOT . '/views/home/404.php';
            return;
        }

        $data = Sanitizer::clean($_POST);

        $before = [
            'title'           => $task->title,
            'description'     => substr($task->description, 0, 50) ?? '',
            'tags'            => $task->tags,
            'cost'            => $task->cost,
            'due_date'        => $task->due_date,
            'expected_hours'  => $task->expected_hours,
            'used_hours'      => $task->used_hours,
            'priority'        => $task->priority,
            'state'           => $task->state
        ];

        // Validacions igual que en store
        $validator = new Validator($data);
        $validator->required('title')->string('title', 3, 255);
        $validator->string('description', 0, 2000);
        $validator->tags('tags');
        $validator->numeric('cost');
        $validator->date('due_date');
        $validator->numeric('expected_hours');
        $validator->numeric('used_hours');
        $validator->enum('priority');
        $validator->enum('state');


        if (!$validator->isValid()) {
            $errors = $validator->errors();
            require APP_ROOT . '/views/layouts/header.php';
            require APP_ROOT . '/views/tasques/edit.php';
            require APP_ROOT . '/views/layouts/footer.php';            
            return;
        }

        $task->title          = $data['title'];
        $task->description    = $data['description'];
        $rawTags              = $data['tags'];
        $task->tags           = array_filter(array_map('trim', explode(',', $rawTags)));
        $task->cost           = $data['cost'];
        $task->due_date       = $data['due_date'];
        $task->expected_hours = $data['expected_hours'];
        $task->used_hours     = $data['used_hours'];
        $task->priority       = $data['priority'];
        $task->state          = $data['state'];

        $task->update();

        $after = [
            'title'           => $task->title,
            'description'     => substr($task->description, 0, 50),
            'tags'            => $task->tags,
            'cost'            => $task->cost,
            'due_date'        => $task->due_date,
            'expected_hours'  => $task->expected_hours,
            'used_hours'      => $task->used_hours,
            'priority'        => $task->priority,
            'state'           => $task->state
        ];

        $changes = [];

        foreach ($after as $key => $value) {
            if ($before[$key] !== $value) {
                $changes[$key] = [
                    'before' => $key === 'description' 
                        ? substr($before[$key], 0, 50) 
                        : $before[$key],
                    'after'  => $key === 'description' 
                        ? substr($value, 0, 50) 
                        : $value
                ];
            }
        }

        $this->logger->info('Tasca actualitzada', [
            'id'      => $task->id,
            'changes' => $changes
        ]);

        header('Location:' . BASE_PATH . '/tasques');
        exit;
    }

    /**
     * Elimina una tasca
     */
    public function delete(int $id): void
    {
        $task = new Task($this->pdo);

        if ($task->load($id)) {

            $before = [
                'id'    => $task->id,
                'title' => $task->title
            ];

            $task->delete();

            $this->logger->warning('Tasca eliminada', [
                'deleted' => true,
                'task'    => $before
            ]);
        }

        header('Location:' . BASE_PATH . '/tasques');
        exit;
    }
}