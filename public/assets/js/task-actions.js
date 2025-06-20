let taskModal = null;

// Visszaadja vagy létrehozza a task modális példányt
function getTaskModalInstance() {
    const el = document.getElementById('taskModal');
    taskModal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
    return taskModal;
}

// Inicializálja az eseményfigyelőt a task form submit eseményére
initTaskActions();

// Új feladat létrehozása modál form megnyitásával
function openTaskForm() {
    initTaskActions(); 
    const taskForm = document.getElementById('task-form');
    if (taskForm) taskForm.reset(); 
    document.getElementById('task_id').value = ''; 
    getTaskModalInstance().show(); 
}

// Létező feladat szerkesztése (AJAX kérés ID alapján)
function editTask(taskId) {
    initTaskActions();
    fetch(`/admin/task/${taskId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        if (!res.ok) throw new Error('Hálózati hiba');
        return res.json(); 
    })
    .then(task => {
        document.getElementById('task_id').value = task.task_id;
        document.getElementById('title').value = task.title;
        document.getElementById('content').value = task.content;
        
        if (task.status !== undefined) {
            const statusSelect = document.getElementById('status');
            if (statusSelect) statusSelect.value = task.status;
        }

        if (task.user_id !== undefined) {
            const userSelect = document.getElementById('user_id');
            if (userSelect) userSelect.value = task.user_id;
        }

        getTaskModalInstance().show();
    })
    .catch(() => showToast('Hiba az adatok lekérésekor', 'danger'));
}

// Feladat törlése megerősítéssel
function deleteTask(taskId) {
    initTaskActions();
    if (!confirm('Biztosan törlöd ezt a hírt?')) return;

    fetch('/admin/tasks', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ delete_task_id: taskId }) 
    })
    .then(res => res.text())
    .then(text => {
        if (text.trim() > 0) {
            showToast('Feladat törölve!', 'success');
            sessionStorage.setItem('toastMessage', 'Feladat törölve!');
            sessionStorage.setItem('toastType', 'success');
            location.reload();
        } else {
            showToast('Hiba történt a törléskor.', 'danger');
        }
    })
    .catch(() => showToast('Hálózati hiba!', 'danger'));
}

// Feladat űrlap beküldésének kezelése (létrehozás/szerkesztés AJAX-on keresztül)
function initTaskActions() {
    const taskForm = document.getElementById('task-form');
    if (!taskForm || taskForm.dataset.bound === 'true') return; 

    taskForm.dataset.bound = 'true'; 

    taskForm.addEventListener('submit', function (e) {
        e.preventDefault(); 

        const formData = new FormData(taskForm); 

        fetch('/admin/tasks', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(text => {
            getTaskModalInstance().hide(); 
            if (text.trim() > 0) {
                showToast('Sikeres mentés!', 'success');
                sessionStorage.setItem('toastMessage', 'Sikeresen mentve!');
                sessionStorage.setItem('toastType', 'success');
                location.reload();
            } else {
                showToast('Sikertelen mentés!', 'danger');
            }
        })
        .catch(() => showToast('Hálózati hiba!', 'danger'));
    });
}

// Feladat státusz módosítása legördülőből, AJAX-al
function updateTaskStatusDropdown(e, elem, taskId) {
    e.preventDefault();
    const newStatus = elem.getAttribute('data-status');

    fetch('/user/task/status/' + taskId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'status=' + encodeURIComponent(newStatus)
    })
    .then(response => response.json())
    .then(data => {
        if (data) {
            const btn = document.getElementById('statusDropdown' + taskId);
            btn.textContent = elem.textContent;
            btn.className = 'btn dropdown-toggle ' + getBadgeClass(newStatus);
            const dropdownMenu = btn.nextElementSibling;
            if (dropdownMenu) {
                dropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    item.classList.remove('active');
                });
                elem.classList.add('active');
            }
            showToast('Sikeres státuszmódosítás!', 'success');
        } else {
            showToast('Sikertelen státuszmódosítás!', 'danger');
        }
    })
    .catch(err => {
        showToast('Hálózati, vagy kapcsolati hiba.', 'danger');
        console.error(err);
    });
}

// A státuszhoz tartozó színosztály kiválasztása
function getBadgeClass(status) {
    switch(status) {
        case 'pending': return 'bg-warning text-dark';
        case 'in_progress': return 'bg-info text-dark';
        case 'completed': return 'bg-success';
        case 'rejected': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
