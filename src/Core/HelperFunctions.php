<?php
// Csak a view kierendelése layout nélkül
function renderView(string $view, array $data = []): string{
    extract($data);
    ob_start();
    require __DIR__ . '/../Views/' . $view . '.php';
    return ob_get_clean();
}
// Layout kirenderelése
function renderLayout(string $content, array $data = []): string{
    extract($data);
    ob_start();
    require __DIR__ . '/../Views/layout.php';
    return ob_get_clean();
}
// Ez dönti el, hogy AJAXOS a kérés, vagy nem.
function isAjaxRequest(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}


