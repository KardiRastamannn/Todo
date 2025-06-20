<style>
    body {
        background-color: #f0f2f5;
    }

    .content-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        margin: 4rem auto;
        padding: 2.5rem 3rem;
        max-width: 900px;
    }

    .task-content {
        font-size: 1rem;
        color: #495057; /* egy normál, jól olvasható szürke */
        margin-top: 1rem;
        line-height: 1.5;
        white-space: pre-line; /* megtartja az esetleges sortöréseket */
        word-wrap: break-word;
    }

    .task-card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 1.8rem 2rem;
        margin-bottom: 2rem;
        background: white;
        transition: box-shadow 0.3s ease;
    }
    .task-card:hover {
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .task-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .task-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #212529;
        flex: 1 1 auto;
        min-width: 200px;
    }

    .task-meta {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
}
    .task-meta i {
        margin-right: 0.35rem;
    }

    /* Státusz dropdown gomb egyedi kinézet */
    .status-dropdown .btn {
        min-width: 140px;
        padding: 0.35rem 0.8rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
    }
    .status-dropdown .btn:focus {
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
    }

    /* Badge színek */
    .bg-warning.text-dark { background-color: #ffc107; color: #212529 !important; }
    .bg-info.text-dark { background-color: #0dcaf0; color: #212529 !important; }
    .bg-success { background-color: #198754; color: #fff !important; }
    .bg-danger { background-color: #dc3545; color: #fff !important; }

    /* Részletek gomb */
    .btn-details {
        margin-top: 1rem;
        font-size: 0.9rem;
    }
</style>

<?php
$statusLabels = [
    'pending' => '🕓 Függőben',
    'in_progress' => '🚧 Folyamatban',
    'completed' => '✅ Befejezve',
    'rejected' => '❌ Visszautasítva'
];
$statusBadges = [
    'pending' => 'bg-warning text-dark',
    'in_progress' => 'bg-info text-dark',
    'completed' => 'bg-success',
    'rejected' => 'bg-danger'
];
?>

<div class="container">
    <div class="content-card">
        <h2 class="mb-4"><i class="fas fa-tasks me-2"></i>Feladatok</h2>

        <?php if (empty($tasks)): ?>
            <div class="alert alert-info text-center" role="alert">
                Jelenleg nincs elérhető feladat. Kérjük, térjen vissza később!
            </div>
        <?php else: ?>
            <?php foreach ($tasks as $task): ?>
                <?php $statusKey = $task['status'] ?? 'pending'; ?>
                <div class="task-card">
                    <div class="task-header">
                        <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>

                        <div class="status-dropdown dropdown">
                            <button
                                class="btn <?= $statusBadges[$statusKey] ?? 'bg-secondary' ?> dropdown-toggle"
                                type="button" id="statusDropdown<?= (int)$task['task_id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $statusLabels[$statusKey] ?? ucfirst($statusKey) ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="statusDropdown<?= (int)$task['task_id'] ?>">
                                <?php foreach ($statusLabels as $key => $label): ?>
                                    <li>
                                        <a href="#"
                                           class="dropdown-item <?= $key === $statusKey ? 'active' : '' ?>"
                                           data-status="<?= $key ?>"
                                           onclick="updateTaskStatusDropdown(event, this, <?= (int)$task['task_id'] ?>)">
                                            <?= $label ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="task-meta">
                        <span><i class="fas fa-calendar-plus"></i> <?= htmlspecialchars($task['created_at']) ?></span>
                    </div>
                    <div class="task-meta">
                        <span><i class="fas fa-calendar-check"></i> <?= htmlspecialchars($task['update_at']) ?></span>
                    </div>
                    <div class="task-content">
                        <?= nl2br(htmlspecialchars($task['content'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>