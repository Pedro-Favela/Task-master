<?php
// ==========================================
// AULA 01: O CÓDIGO SPAGHETTI (Tudo misturado)
// ==========================================

// 1. CONEXÃO COM O BANCO DE DADOS E CRIAÇÃO DA TABELA (Acoplamento de Infraestrutura)
$dbFile = __DIR__ . '/tasks.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    due_date TEXT NOT NULL,
    responsible TEXT NOT NULL,
    done INTEGER DEFAULT 0
)");

// 2. LÓGICA DE NEGÓCIO E CONTROLE DE REQUISIÇÕES MISTURADOS
$error = '';

// Criar nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');
    $responsible = trim($_POST['responsible'] ?? '');
    
    // Regra de negócio solta no meio do arquivo
    if (empty($title)) {
        $error = "O título da tarefa não pode estar vazio!";
    } elseif (empty($due_date)) {
        $error = "A data de vencimento é obrigatória!";
    } elseif (empty($responsible)) {
        $error = "O responsável é obrigatório!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, responsible) VALUES (:title, :description, :due_date, :responsible)");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':due_date', $due_date);
        $stmt->bindValue(':responsible', $responsible);
        $stmt->execute();
        
        // Redirecionamento misturado com a lógica
        header("Location: index.php");
        exit;
    }
}

// Concluir ou excluir tarefa
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if ($_GET['action'] === 'complete') {
        $pdo->exec("UPDATE tasks SET done = 1 WHERE id = $id");
    } elseif ($_GET['action'] === 'delete') {
        $pdo->exec("DELETE FROM tasks WHERE id = $id");
    }
    
    header("Location: index.php");
    exit;
}

// 3. BUSCA DE DADOS MISTURADA COM A VISUALIZAÇÃO
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Master - Spaghetti</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f3f4f6; 
            color: #333; 
            display: flex; 
            justify-content: center; 
            padding-top: 50px; 
        }
        .container { 
            background: #fff; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 500px; 
        }
        h1 { 
            font-size: 1.5rem; 
            text-align: center; 
            border-bottom: 2px solid #eee; 
            padding-bottom: 10px; 
        }
        .error { 
            color: #dc2626; 
            background: #fee2e2; 
            padding: 10px; 
            border-radius: 4px; 
            font-size: 0.9rem; 
        }
        .form-group { 
            display: flex; 
            gap: 10px; 
            margin-top: 20px; 
            margin-bottom: 20px; 
        }
        input[type="text"], input[type="date"], textarea { 
            flex: 1; 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
        }
        .form-fields {
            display: grid;
            gap: 10px;
            margin-top: 10px;
        }
        .form-fields input, .form-fields textarea {
            width: 100%;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 60px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        button { 
            background: #2563eb; 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        button:hover { 
            background: #1d4ed8; 
        }
        ul { 
            list-style: none; 
            padding: 0; 
        }
        li { 
            padding: 12px; 
            border-bottom: 1px solid #eee; 
        }
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .task-details {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 8px;
        }
        .task-details p {
            margin: 4px 0;
        }
        .detail-label {
            font-weight: bold;
        }
        li.done span { 
            text-decoration: line-through; 
            color: #9ca3af; 
        }
        .actions a { 
            text-decoration: none; 
            margin-left: 10px; 
            cursor: pointer; 
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Task Master (Spaghetti Edition)</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <div class="form-group">
            <input type="text" name="title" placeholder="Título da tarefa" autocomplete="off" required>
            <button type="submit">Adicionar</button>
        </div>
        <div class="form-fields">
            <textarea name="description" placeholder="Descrição (opcional)"></textarea>
            <input type="date" name="due_date" required>
            <input type="text" name="responsible" placeholder="Responsável (obrigatório)" required>
        </div>
    </form>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?php echo $task['done'] ? 'done' : ''; ?>">
                <div class="task-header">
                    <span><?php echo htmlspecialchars($task['title']); ?></span>
                    <div class="actions">
                        <?php if (!$task['done']): ?>
                            <a href="?action=complete&id=<?php echo $task['id']; ?>" title="Concluir">✅</a>
                        <?php endif; ?>
                        <a href="?action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');" title="Excluir">❌</a>
                    </div>
                </div>
                <div class="task-details">
                    <p><span class="detail-label">Responsável:</span> <?php echo htmlspecialchars($task['responsible']); ?></p>
                    <p><span class="detail-label">Vencimento:</span> <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></p>
                    <?php if (!empty($task['description'])): ?>
                        <p><span class="detail-label">Descrição:</span> <?php echo htmlspecialchars($task['description']); ?></p>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>