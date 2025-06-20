// Visszaadja vagy létrehozza a felhasználó modált (Bootstrap Modal példány)
function getUserModalInstance() {
    const el = document.getElementById('userModal');
    userModal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
    return userModal;
}

// Megnyitja az új felhasználó létrehozó űrlapot, formot alaphelyzetbe állítja
function openUserForm() {
    initUserActions(); // biztosítja, hogy a submit esemény kezelve legyen
    const form = document.getElementById('user-form');
    if (form) form.reset(); // mezők kiürítése
    document.getElementById('user_id').value = ''; // új rekordnál ne legyen ID
    getUserModalInstance().show(); // modál megjelenítése
}

// Meglévő felhasználó adatainak betöltése szerkesztéshez (AJAX-szal)
function editUser(id) {
    initUserActions();
    fetch(`/admin/users/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json()) // válasz JSON formátumban
    .then(user => {
        // Form mezők kitöltése a betöltött adatokkal
        document.getElementById('user_id').value = user[0].user_id;
        document.getElementById('email').value = user[0].email;
        document.getElementById('role').value = user[0].role;
        getUserModalInstance().show(); // modál megnyitása szerkesztéshez
    })
    .catch(() => showToast('Hiba a felhasználó betöltésekor', 'danger'));
}

// Felhasználó törlése megerősítés után (AJAX POST)
function deleteUser(id) {
    initUserActions();
    if (!confirm('Biztosan törlöd ezt a felhasználót?')) return;

    fetch('/admin/users', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ delete_user_id: id }) 
    })
    .then(res => res.text())
    .then(text => {
        if (text.trim() > 0) {
            showToast('Sikeres törlés!', 'success');
            sessionStorage.setItem('toastMessage', 'Sikeres törlés!');
            sessionStorage.setItem('toastType', 'success');
            location.reload();
        } else {
            showToast('Sikertelen törlés!', 'danger');
        }
    })
    .catch(() => showToast('Sikertelen törlés!', 'danger'));
}

// A felhasználó űrlap submit eseményét kezeli.
function initUserActions() {
    const form = document.getElementById('user-form');
    if (!form || form.dataset.bound === 'true') return;

    form.dataset.bound = 'true';

    form.addEventListener('submit', function (e) {
        e.preventDefault(); 

        const formData = new FormData(form);

        fetch('/admin/users', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(text => {
            getUserModalInstance().hide(); 
            if (text.trim() > 0) {
                showToast('Sikeres mentés!', 'success');
                sessionStorage.setItem('toastMessage', 'Sikeresen mentve!');
                sessionStorage.setItem('toastType', 'success');
                location.reload();
            } else {
                showToast('Sikertelen mentés!', 'danger');
            }
        })
        .catch(() => showToast('Sikertelen mentés!', 'danger'));
    });
}
