# Seguretat — Blog MVC

## 🛡️ Resum

L'aplicació implementa cinc mesures de seguretat principals que cobreixen les vulnerabilitats més comunes en aplicacions web: CSRF, SQL Injection, XSS, accés no autoritzat i emmagatzematge insegur de contrasenyes.

---

## 1. Protecció CSRF

**Vulnerabilitat que prevé:** Cross-Site Request Forgery — atacs que forcen un usuari autenticat a executar accions no desitjades en una aplicació on té sessió activa.

### Implementació

Tots els formularis POST inclouen un token CSRF ocult generat aleatòriament:

```php
// Generació del token (Csrf.php)
public static function generate(): string
{
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

// Camp ocult al formulari
public static function field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . self::generate() . '">';
}
```

Cada controller POST valida el token abans d'executar res:

```php
public static function validate(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        Redirect::withError('/', 'Token CSRF invàlid.');
        exit;
    }
}
```

L'ús de `hash_equals()` prevé atacs de timing on es compara el token caràcter per caràcter.

### Cobertura

Totes les rutes POST de l'aplicació estan protegides: login, registre, logout, creació/edició/eliminació de posts, actualització de perfil, avatar i totes les accions administratives.

---

## 2. SQL Injection — Prepared Statements

**Vulnerabilitat que prevé:** Injecció SQL — inserció de codi SQL maliciós a través de dades d'entrada per manipular la base de dades.

### Implementació

Totes les consultes a la base de dades utilitzen **PDO amb prepared statements**. Cap valor d'usuari s'interpola directament a les queries:

```php
// ❌ MALAMENT — vulnerable a SQL Injection
$stmt = $pdo->query("SELECT * FROM users WHERE email = '$email'");

// ✅ BÉ — prepared statement amb paràmetres
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
```

La connexió PDO es configura amb el mode d'errors adequat i l'emulació de prepared statements desactivada per garantir que les consultes es preparen realment al servidor MySQL:

```php
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

---

## 3. XSS — Escapament de sortida

**Vulnerabilitat que prevé:** Cross-Site Scripting — injecció de codi JavaScript maliciós a través de contingut mostrat a les vistes.

### Implementació

Tot el contingut dinàmic mostrat a les vistes passa per `htmlspecialchars()` amb la flag `ENT_QUOTES`:

```php
// A totes les vistes
<?= htmlspecialchars($post->title, ENT_QUOTES) ?>
<?= htmlspecialchars($user['name'], ENT_QUOTES) ?>
<?= htmlspecialchars($data['email'] ?? '—', ENT_QUOTES) ?>
```

`ENT_QUOTES` converteix tant cometes simples com dobles, cobrint tots els contextos HTML on el valor pot aparèixer (atributs, text, etc.).

### Cobertura

Totes les vistes de l'aplicació escapen el contingut dinàmic sense excepció: títols, noms, emails, contingut de posts, dades del historial d'auditoria i missatges flash.

---

## 4. Middleware d'autenticació i autorització

**Vulnerabilitat que prevé:** Accés no autoritzat a rutes i recursos protegits.

### Implementació

Quatre middleware independents gestionen el control d'accés. Cada controller els crida explícitament al principi de cada mètode:

#### `AuthMiddleware` — Requereix sessió activa
```php
public static function handle(): void
{
    if (!isset($_SESSION['user_id'])) {
        Redirect::withError('/login', 'Has d\'iniciar sessió per accedir.');
        exit;
    }
}
```

#### `GuestMiddleware` — Requereix que NO hi hagi sessió
```php
public static function handle(): void
{
    if (isset($_SESSION['user_id'])) {
        Redirect::to('/');
        exit;
    }
}
```

#### `OwnerMiddleware` — Requereix ser l'autor del recurs
```php
public static function handle(int $postId): Post
{
    AuthMiddleware::handle();
    $post = new Post();
    if (!$post->load($postId) || (int)$post->author_id !== (int)$_SESSION['user_id']) {
        Redirect::withError('/my-posts', 'No tens permís per modificar aquest post.');
        exit;
    }
    return $post;
}
```

#### `AdminMiddleware` — Requereix rol `admin`
```php
public static function handle(): void
{
    AuthMiddleware::handle();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        Redirect::withError('/', 'Accés restringit.');
        exit;
    }
}
```

Tots els middleware disposen també d'un mètode `check()` que retorna `bool` sense redirigir, per ús condicional a les vistes (per exemple, mostrar o amagar botons d'edició).

---

## 5. Hash de contrasenyes

**Vulnerabilitat que prevé:** Exposició de contrasenyes en text pla en cas de breach de la base de dades.

### Implementació

Les contrasenyes mai es guarden en text pla. S'utilitza `password_hash()` amb l'algoritme `PASSWORD_DEFAULT` (bcrypt):

```php
// Registre i canvi de contrasenya
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
```

La verificació es fa amb `password_verify()`:

```php
// Login
if (!password_verify($password, $user['password'])) {
    // Credencials incorrectes
}
```

`PASSWORD_DEFAULT` utilitza bcrypt amb un cost de 10 per defecte, que inclou salt automàtic i és suficientment lent per dificultar atacs de força bruta.

### Requisits de contrasenya

Les contrasenyes es validen al servidor amb els requisits mínims següents:
- Mínim 8 caràcters
- Almenys 1 lletra majúscula
- Almenys 1 número
- Almenys 1 símbol

---

## 6. Validació de dades d'entrada

**Vulnerabilitat que prevé:** Dades malformades, inesperades o malicioses que poden causar errors o comportaments inesperats.

### Implementació

Totes les dades dels formularis es validen al servidor abans de processar-les. La validació inclou:

- **Camps obligatoris** — verificació que no estiguin buits
- **Longituds** — mínim i màxim de caràcters
- **Formats** — email vàlid, format de contrasenya
- **Unicitat** — email únic a la base de dades
- **Enums** — valors permesos per `status` i `role`
- **Fitxers** — tipus MIME i mida màxima per avatars

En cas d'errors, el formulari es torna a mostrar amb els errors inline al costat de cada camp i les dades prèviament introduïdes conservades per no obligar l'usuari a repetir-ho tot.

---

## 7. Seguretat de fitxers i directoris

### Punt d'entrada únic

El fitxer `.htaccess` a `public/` redirigeix totes les peticions a `index.php`, excepte els fitxers estàtics existents. Tot el codi font queda fora del directori públic:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Uploads

Els avatars es guarden a `public/storage/uploads/avatars/` amb nom de fitxer generat aleatòriament (`uniqid()`) per evitar endevinació de rutes i sobreescriptura de fitxers:

```php
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename  = uniqid('avatar_', true) . '.' . $extension;
```

### Variables d'entorn

Les credencials de la base de dades i altres configuracions sensibles es gestionen amb **dotenv** a través del fitxer `.env`, que mai es puja al repositori (inclòs al `.gitignore`).

### Logs

La carpeta de logs conté un `.gitkeep` però els fitxers `.log` estan al `.gitignore` per evitar exposar informació sensible al repositori.

---

## 📋 Resum de vulnerabilitats cobertes

| Vulnerabilitat | Mesura implementada |
|----------------|---------------------|
| CSRF | Tokens CSRF a tots els formularis POST |
| SQL Injection | PDO prepared statements a totes les consultes |
| XSS | `htmlspecialchars()` a totes les sortides |
| Accés no autoritzat | Middleware d'autenticació i autorització |
| Contrasenyes en text pla | `password_hash()` amb bcrypt |
| Dades malformades | Validació al servidor en tots els formularis |
| Exposició de codi font | Punt d'entrada únic, codi fora de `public/` |
| Exposició de credencials | Variables d'entorn amb dotenv |
