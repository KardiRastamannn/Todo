<style>
    body {
        background-color: #f8f9fa;
    }
    .content-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin: 2rem auto;
        padding: 2rem;
    }
    .table th {
        background-color: #f8f9fa;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
    }
</style>
<div class="container">
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Feladatok kezelése
            </h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="openTaskForm()">
                <i class="fas fa-plus me-2"></i>Új feladat
            </button>
        </div>
        <div class="table-responsive mb-4">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Cím</th>
                        <th>Hozzárendelt felhasználó</th>
                        <th>Tartalom</th>
                        <th>Státusz</th>
                        <th>Létrehozva</th>
                        <th>Utoljára frissítve</th>
                        <th>Akciók</th>
                    </tr>
                </thead>
                <tbody id="tasks-table-body">
                    <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Jelenleg nincs egyetlen feladat sem.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                        <tr data-task-id="<?= htmlspecialchars($task['task_id']) ?>">
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= htmlspecialchars($task['email']) ?></td>
                            <td><?= htmlspecialchars($task['content']) ?></td>
                            <td>
                                <?php 
                                    $statusBadges = [
                                        'pending' => 'badge bg-warning text-dark',
                                        'in_progress' => 'badge bg-info text-dark',
                                        'completed' => 'badge bg-success',
                                        'rejected' => 'badge bg-danger'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Függőben',
                                        'in_progress' => 'Folyamatban',
                                        'completed' => 'Befejezve',
                                        'rejected' => 'Visszautasítva'
                                    ];
                                    $statusKey = $task['status'];
                                    $badgeClass = $statusBadges[$statusKey] ?? 'badge bg-secondary';
                                    $label = $statusLabels[$statusKey] ?? htmlspecialchars($statusKey);
                                    echo "<span class=\"$badgeClass\">$label</span>";
                                ?>
                            </td>
                            <td><?= htmlspecialchars($task['created_at']) ?></td>
                            <td><?= htmlspecialchars($task['update_at']) ?></td>
                            <td>
                              <div class="d-flex flex-nowrap gap-1">
                                  <button class="btn btn-warning btn-sm" onclick="editTask(<?= htmlspecialchars($task['task_id']) ?>)">
                                      <i class="fas fa-edit"></i>
                                  </button>
                                  <button class="btn btn-danger btn-sm" onclick="deleteTask(<?= htmlspecialchars($task['task_id']) ?>)">
                                      <i class="fas fa-trash"></i>
                                  </button>
                              </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal a poszt űrlaphoz -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="task-form" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title" id="taskModalLabel">Feladat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="task_id" id="task_id">

        <div class="mb-3">
          <label for="title" class="form-label"><i class="fas fa-heading me-2"></i>Cím</label>
          <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="user_id" class="form-label"><i class="fas fa-user me-2"></i>Hozzárendelt felhasználó</label>
          <select name="user_id" id="user_id" class="form-select" required>
            <option value="" disabled selected>Válassz felhasználót</option>
            <?php foreach ($users as $user): ?>
              <option value="<?= htmlspecialchars($user['user_id']) ?>">
                <?= htmlspecialchars($user['email']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label for="content" class="form-label"><i class="fas fa-file-alt me-2"></i>Tartalom</label>
          <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
        </div>

        <div class="mb-3">
          <label for="status" class="form-label"><i class="fas fa-info-circle me-2"></i>Státusz</label>
          <select name="status" id="status" class="form-select" required>
            <option value="pending">Függőben</option>
            <option value="in_progress">Folyamatban</option>
            <option value="completed">Befejezve</option>
            <option value="rejected">Visszautasítva</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">
          <i class="fas fa-save me-2"></i>Mentés
        </button>
      </div>
    </form>
  </div>
</div>