<?php
// Sistema de Autoloading Nativo (PSR-0 simplificado)
spl_autoload_register(function ($class) {
    $dirs = ['Model', 'Controller', 'View'];
    foreach ($dirs as $dir) {
        $file = __DIR__ . "/src/$dir/$class.php";
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// 1. Conexão com o banco (Único lugar no sistema inteiro!)
$pdo = new PDO('sqlite:' . __DIR__ . '/tasks.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Criar tabela se não existir
$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    due_date TEXT NOT NULL,
    responsible TEXT NOT NULL,
    done INTEGER DEFAULT 0
)");

// Migração: adicionar colunas se não existirem
try {
    $stmt = $pdo->query("PRAGMA table_info(tasks)");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    
    if (!in_array('description', $columns)) {
        $pdo->exec("ALTER TABLE tasks ADD COLUMN description TEXT");
    }
    if (!in_array('due_date', $columns)) {
        $pdo->exec("ALTER TABLE tasks ADD COLUMN due_date TEXT DEFAULT '2026-05-13'");
    }
    if (!in_array('responsible', $columns)) {
        $pdo->exec("ALTER TABLE tasks ADD COLUMN responsible TEXT DEFAULT 'Sem responsável'");
    }
} catch (Exception $e) {
    // Se a tabela não existir, será criada acima
}

// 2. Roteamento básico
$controller = new TaskController($pdo);
$action = $_GET['action'] ?? 'index'; // Se não vier action, usa 'index'

if (method_exists($controller, $action)) {
    $controller->$action(); // Executa o método correspondente
} else {
    echo "Página não encontrada 404";
}
?>