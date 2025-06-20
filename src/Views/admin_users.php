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
    #content {
        min-height: 200px;
        resize: vertical;
    }
</style>
<div class="container">
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-users me-2"></i>Felhasználók kezelése</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openUserForm()">+ Új felhasználó</button>
        </div>
        <!-- Felhasználói tábla -->
        <div class="table-responsive mb-4">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th><th>Email</th><th>Szerepkör</th><th>Akciók</th>
                    </tr>
                </thead>
                <tbody id="user-table-body">
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Nincs egyetlen felhasználó sem.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr data-user-id="<?= htmlspecialchars($user['user_id']) ?>">
                                <td><?= htmlspecialchars($user['user_id']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editUser(<?= htmlspecialchars($user['user_id']) ?>)"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= htmlspecialchars($user['user_id']) ?>)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal (form) -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="user-form" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Felhasználó</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="user_id" id="user_id">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Jelszó</label>
          <input type="password" name="password" id="password" class="form-control">
        </div>
        <div class="mb-3">
          <label for="role" class="form-label">Szerepkör</label>
          <select name="role" id="role" class="form-select">
            <option value="user">Felhasználó</option>
            <option value="admin">Admin</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Mentés</button>
      </div>
    </form>
  </div>
</div>