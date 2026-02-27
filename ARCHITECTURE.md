# Arquitectura — Blog MVC

## 📐 Patró MVC

L'aplicació segueix el patró **Model-View-Controller (MVC)**, que separa les responsabilitats en tres capes independents:

- **Model** — gestiona les dades i la lògica de negoci
- **View** — renderitza la interfície d'usuari
- **Controller** — rep les peticions, coordina el model i retorna la vista

```
Petició HTTP
     │
     ▼
  Router
     │
     ▼
Controller ──── Model ──── Base de Dades
     │
     ▼
   Vista
     │
     ▼
Resposta HTTP
```

---

## 📁 Estructura de carpetes

```
src/
├── Domain/          → Models i lògica de negoci
├── Http/            → Controllers, Middleware, Router i Sessions
├── Infrastructure/  → Base de dades, Seguretat i Logging
└── Views/           → Plantilles HTML/PHP
```

El punt d'entrada únic de l'aplicació és `public/index.php`. Tot el codi font queda fora del directori públic i és inaccessible directament des del navegador.

---

## 🗂️ Domain

Conté la lògica de negoci pura, independent de HTTP.

### Models (`Domain/Models/`)

Cada model representa una entitat de la base de dades i encapsula totes les operacions relacionades amb ella.

| Fitxer | Responsabilitat |
|--------|-----------------|
| `User.php` | Gestió de l'usuari: càrrega, actualització, avatar |
| `Post.php` | CRUD de posts, paginació, cerca, visualitzacions |
| `Admin.php` | Estadístiques globals, gestió administrativa, auditoria |

Els models ofereixen dos tipus de mètodes:

- **Mètodes d'instància** — operen sobre un registre concret (`load()`, `save()`, `update()`, `delete()`)
- **Mètodes estàtics** — retornen col·leccions de dades (`findAll()`, `findAllPaginated()`, `search()`)

### Services (`Domain/Services/`)

| Fitxer | Responsabilitat |
|--------|-----------------|
| `AuthService.php` | Lògica d'autenticació: login, registre, logout |

### Slug (`Domain/Slug/`)

| Fitxer | Responsabilitat |
|--------|-----------------|
| `SlugGenerator.php` | Generació de slugs únics i SEO-friendly a partir del títol |

---

## 🌐 Http

Gestiona tot el relacionat amb la capa HTTP.

### Controllers (`Http/Controllers/`)

Cada controller rep una petició, crida els models necessaris i retorna una vista o una redirecció. Cap controller conté lògica de base de dades directament.

| Fitxer | Rutes que gestiona |
|--------|-------------------|
| `HomeController.php` | `/` |
| `AuthController.php` | `/login`, `/register`, `/logout` |
| `PostController.php` | `/posts`, `/my-posts`, `/search`, `/author/{id}` |
| `UserController.php` | `/profile` |
| `AdminController.php` | `/admin/*` |

Tots els controllers segueixen el mateix patró:

```php
public function metode(string $param): void
{
    // 1. Middleware (auth, admin, owner...)
    MiddlewareName::handle();

    // 2. Validació CSRF (si és POST)
    Csrf::validate();

    // 3. Lògica i crida al model
    $data = Model::findSomething();

    // 4. Renderitzar vista o redirigir
    $this->render('view/name', ['data' => $data]);
    // o
    Redirect::withSuccess('/ruta', 'Missatge');
}
```

### Middleware (`Http/Middleware/`)

Els middleware s'executen abans de la lògica del controller per controlar l'accés.

| Fitxer | Responsabilitat |
|--------|-----------------|
| `AuthMiddleware.php` | Verifica que l'usuari té sessió activa. Redirigeix a `/login` si no. |
| `GuestMiddleware.php` | Verifica que l'usuari NO té sessió activa. Redirigeix a `/` si ja està autenticat. |
| `OwnerMiddleware.php` | Verifica que l'usuari és l'autor del post. Redirigeix amb error si no. |
| `AdminMiddleware.php` | Verifica que l'usuari té rol `admin`. Redirigeix amb error si no. |

Cada middleware té dos mètodes:

- `handle()` — aplica la restricció i redirigeix si no es compleix
- `check()` — retorna `true` o `false` sense redirigir (per ús condicional a les vistes)

### Router (`Http/Routing/`)

| Fitxer | Responsabilitat |
|--------|-----------------|
| `Router.php` | Parseja la URL, detecta paràmetres dinàmics i crida el controller corresponent |
| `Redirect.php` | Centralitza les redireccions amb missatges flash |

El Router suporta paràmetres dinàmics com `{id}` o `{slug}` i gestiona errors 404. Totes les rutes es defineixen a `config/routes.php`.

### Sessions (`Http/Session/`)

| Fitxer | Responsabilitat |
|--------|-----------------|
| `Session.php` | Abstracció sobre `$_SESSION` amb mètodes `get()`, `set()`, `forget()` |

---

## ⚙️ Infrastructure

Capa transversal que dóna suport a tota l'aplicació.

### Base de dades (`Infrastructure/Database/`)

| Fitxer | Responsabilitat |
|--------|-----------------|
| `Database.php` | Connexió PDO amb patró **Singleton** — una sola connexió per petició |
| `dbManager.php` | Executa les migracions SQL per crear la BD i les taules |
| `DatabaseSeeder.php` | Genera dades de prova per a desenvolupament |

### Seguretat (`Infrastructure/Security/`)

| Fitxer | Responsabilitat |
|--------|-----------------|
| `Csrf.php` | Generació i validació de tokens CSRF per protegir els formularis POST |

### Logging (`Infrastructure/Logging/`)

Integració amb **Monolog** per registrar esdeveniments en fitxers separats:

| Fitxer de log | Contingut |
|---------------|-----------|
| `mvcApp.log` | Esdeveniments generals de l'aplicació |
| `mvcError.log` | Errors i excepcions |
| `mvcWarnings.log` | Avisos i situacions inesperades |

---

## 🖼️ Views

Les vistes són plantilles PHP pures sense lògica de negoci. Reben les dades del controller via `extract()` i les mostren amb `htmlspecialchars()` per prevenir XSS.

### Layout

Totes les pàgines públiques comparteixen un layout comú:

```
header.php  →  [vista específica]  →  footer.php
```

El `header.php` inclou Bootstrap 5, Bootstrap Icons i el CSS personalitzat. El `footer.php` inclou el JS de Bootstrap i tanca el document.

### Estructura de vistes

```
Views/
├── layouts/     → header.php, footer.php
├── home/        → home.php, 404.php
├── auth/        → login.php, register.php
├── posts/       → index.php, show.php, create.php, edit.php, my-posts.php
├── user/        → profile.php, edit.php
└── admin/       → index.php, users.php, posts.php, audit.php, sidebar.php
```

Les vistes d'administració no usen el layout general — inclouen directament el `sidebar.php` i gestionen el seu propi layout amb Bootstrap.

---

## 🔄 Flux d'una petició

Exemple: `POST /my-posts` (crear un post)

```
1. Navegador envia POST /my-posts
2. Apache .htaccess redirigeix tot a public/index.php
3. index.php carrega .env, constants i el Router
4. Router parseja /my-posts → PostController::store()
5. store() crida AuthMiddleware::handle() → usuari autenticat ✓
6. store() valida el token CSRF → vàlid ✓
7. store() valida i saneja les dades del formulari
8. store() crea una instància de Post i crida save()
9. Post::save() executa INSERT amb PDO prepared statement
10. store() crida Redirect::withSuccess('/my-posts', 'Post creat!')
11. Redirect guarda el missatge a $_SESSION i fa header Location
12. Navegador rep 302 → GET /my-posts
13. Router → PostController::myPosts()
14. myPosts() carrega els posts de l'usuari
15. render() inclou header.php + my-posts.php + footer.php
16. header.php llegeix i esborra el missatge flash de la sessió
17. Navegador rep la vista amb el missatge d'èxit
```

---

## 🎨 Estil i Frontend

L'estil segueix una separació clara de responsabilitats:

- **Bootstrap 5** — estructura, grid, layout, components i responsive
- **Bootstrap Icons** — iconografia consistent
- **CSS personalitzat (`style.css`)** — colors, ombres, tipografia i elements visuals específics que Bootstrap no cobreix

Aquesta separació permet mantenir Bootstrap com a base estructural sense dependre'n per a l'estil visual, facilitant futurs canvis de disseny.
