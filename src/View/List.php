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
    <h1>Task Master (MVC Edition)</h1>
   
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- O formulário agora aponta para a action 'create' -->
    <form method="POST" action="index.php?action=create">
        <div class="form-group">
            <input type="text" name="title" placeholder="Título" required>
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
                            <a href="index.php?action=complete&id=<?php echo $task['id']; ?>" title="Concluir">✅</a>
                        <?php endif; ?>
                        <a href="index.php?action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');" title="Excluir">❌</a>
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