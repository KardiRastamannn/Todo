Rövid leírás
Ez egy feladatkezelő alkalmazás, ahol van külön admin és felhasználó szerepkör.
Az admin tud kizárólag felvinni új felhasználókat, új feladatokat és hozzárendelni felhasználókhoz, míg a felhasználó csak a saját feladait látja és azok státuszait módosíthatja.

Követelmények

- PHP 8.0 vagy újabb
- MySQL (pl. XAMPP, Laragon vagy külön adatbázisszerver)
- Composer (csak az autoloadolás miatt)
- Webszerver (Apache vagy Nginx)

Telepítés

1. A `Connection.php` fájlban állítsuk be az adatbázis kapcsolatot.
2. Klónozzuk a projektet Git-ről.
3. Az adatbázist a `public/sql/tasks.sql` fájl alapján hozzuk létre.
4. Futtassuk a `composer install` parancsot.
   (Megjegyzés: nincs külső csomag, de az autoload szükséges.)
5. Indítsuk el a webszervert.

Az sql fájl tartalmaz egy teszt felhasználó beszúrást is, mert jelenleg nincs lehetőség regisztrációra, és csak egy admin tud új felhasználót felvinni.
Természetesen ezzel a felhasználóval, akár lehet majd új felhasználókat is felvinni.

- Felhasználónév: intrum@test.hu
- Jelszó: asd

1. Főbb Architekturális Döntések
MVC Modell: A projekt szigorúan követi a Model-View-Controller architektúrát:

- Modellek: pl. TasksModel, UserModel – kizárólag adatbázis-műveletekhez.
- View-k: sima .php fájlok, dinamikus HTML tartalommal – pl. admin_tasks.php, guest_home.php.
  A layout.php biztosít egységes keretet, a renderLayout() segítségével tölti be a tartalmat.
- Controller-ek: pl. AdminController, GuestController, TasksController, UserController – ezek kezelik a kéréseket, és közvetítik az adatokat a nézetek felé.

Dependency Injection (DI):

- A Container osztály központilag kezeli a függőségeket (pl. adatbáziskapcsolat, controllerek).

2. Adatbázis Réteg

- PDO használata: A Connection osztály absztrahálja a PDO-t, biztosítva az SQL injekció elleni védelmet.
- Metódusok: pdoSelect() és pdoQuery metódusokkal történik a lekérdezés és módosítás.
- Táblák: users (felhasználók), tasks (feladatok, kapcsolat user_id-n keresztül).
- Relációk: Minden feladathoz tartozik egy felhasználó.

3. Biztonsági Megoldások

- Hitelesítés: Az AuthService kezeli a bejelentkezést $_SESSION segítségével. A jelszavak a password_hash()-sel vannak titkosítva.
- Jogosultság: Az isAdmin() metódus határozza meg, hogy egy felhasználó admin-e.
- Input kezelés:
  - HTML escape: htmlspecialchars() + nl2br() a megjelenítéshez
  - Minden adatbázis lekérdezés paraméterezve történik, pl.:
    $this->db->pdoSelect("SELECT * FROM users WHERE email = ?", [$email])
	
4. Frontend és UX

- Reszponzív design: Bootstrap 5
- Animációk: Pl. animált 404-es oldal
- Modális ablakok: feladat létrehozás, szerkesztés stb.
- AJAX:
  - Az isAjaxRequest() alapján történik dinamikus frissítés.
  - showToast() függvény jeleníti meg a visszajelzéseket.

5. Fontosabb komponensek

- Router:
  A web.php fájlban definiáljuk az útvonalakat, pl.:
  'admin/tasks' => [TasksController::class, 'handleRequest']

- Hibakezelés:
  - Egyedi 404-es oldal animációval
  - Hibák logolása a Connection osztályon keresztül (error_log())

6. Tervek a jövőre
- Regisztráció implementálása
- GET/POST útvonalak külön kezelése a routerben
- AJAX-os újratöltése a táblázatoknak.
- Nézetek betöltésének átalakítása külön View osztályra
- CSRF tokenek bevezetése
- .env fájl környezeti változóknak
- Jelszó-erősség ellenőrzés
- Error osztály létrehozása a hibakezeléshez
- Komponens alapú fejlesztés: Pl table.php ,vagy form.php és aztán ebből származtatni az új osztályokat.
- 2FA azonosítás
- Memcached használata gyakran lekérdezett adatokhoz.
- Low-code admin felület
- Language model bevezetése, dictionary használata, traitek használata.
- JS fájlok betöltése kizárólag akkor, ha a view is betöltődik.





