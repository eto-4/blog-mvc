# Blog MVC — PHP OOP

Sistema de blog complet desenvolupat amb arquitectura MVC, autenticació d'usuaris, gestió de posts i panell d'administració.

---

## 🚀 Tecnologies

**Frontend**
- HTML5
- Bootstrap 5
- CSS personalitzat

**Backend**
- PHP 8.2 (OOP)
- MySQL amb PDO
- Arquitectura MVC

---

## 📁 Estructura de Carpetes

```
blog-mvc/
│   .env
│   .envExample
│   .gitignore
│   composer.json
│   composer.lock
│   README.md
│
├───config/
│       routes.php
│
├───public/
│   │   .htaccess
│   │   index.php
│   │
│   └───assets/
│       ├───css/
│       │       style.css
│       ├───imgs/
│       ├───js/
│       │       app.js
│       └───storage/
│           └───uploads/
│
└───src/
    ├───Domain/
    │   ├───Models/
    │   │       Admin.php
    │   │       Post.php
    │   │       User.php
    │   │
    │   ├───Services/
    │   │       AuthService.php
    │   │
    │   └───Slug/
    │           SlugGenerator.php
    │
    ├───Http/
    │   ├───Controllers/
    │   │       AdminController.php
    │   │       AuthController.php
    │   │       HomeController.php
    │   │       PostController.php
    │   │       UserController.php
    │   │
    │   ├───Middleware/
    │   │       AdminMiddleware.php
    │   │       AuthMiddleware.php
    │   │       GuestMiddleware.php
    │   │       OwnerMiddleware.php
    │   │
    │   ├───Routing/
    │   │       Redirect.php
    │   │       Router.php
    │   │
    │   └───Session/
    │           Session.php
    │
    ├───Infrastructure/
    │   ├───Database/
    │   │   ├───DatabaseCore/
    │   │   │       Database.php
    │   │   │
    │   │   ├───Seeders/
    │   │   │       DatabaseSeeder.php
    │   │   │
    │   │   └───dbManager/
    │   │       │   dbManager.php
    │   │       └───dbMigrations/
    │   │               create_database.sql
    │   │               create_tables.sql
    │   │
    │   ├───Logging/
    │   │   └───logs/
    │   │           .gitkeep
    │   │           mvcApp.log
    │   │           mvcError.log
    │   │           mvcWarnings.log
    │   │
    │   └───Security/
    │           Csrf.php
    │
    └───Views/
        ├───admin/
        │       audit.php
        │       index.php
        │       posts.php
        │       sidebar.php
        │       users.php
        │
        ├───auth/
        │       login.php
        │       register.php
        │
        ├───home/
        │       404.php
        │       home.php
        │
        ├───layouts/
        │       footer.php
        │       header.php
        │
        ├───posts/
        │       create.php
        │       edit.php
        │       index.php
        │       my-posts.php
        │       show.php
        │
        └───user/
                edit.php
                profile.php
```

---

## ⚙️ Instal·lació

1. Clona o descarrega el repositori
2. Obre la terminal a l'arrel del projecte
3. Instal·la les dependències:
```bash
composer install
```
4. Crea el fitxer `.env` a partir de `.envExample` i omple les dades:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=blog_mvc
DB_USER=root
DB_PASS=

APP_ENV=local
BASE_PATH=
```
5. Executa les migracions per crear la base de dades i les taules:
```bash
php src/Infrastructure/Database/dbManager/dbManager.php
```
6. (Opcional) Executa el seeder per generar dades de prova:
```bash
php src/Infrastructure/Database/Seeders/DatabaseSeeder.php
```

**Credencials de prova del seeder:**
- Admin: `admin@blog.com` / `Admin123!`
- Usuari: `john@blog.com` / `User123!`

---

## 🗺️ Rutes

### Públiques
| Mètode | Ruta | Descripció |
|--------|------|------------|
| GET | `/` | Pàgina principal |
| GET | `/posts` | Llistat de posts publicats |
| GET | `/posts/{slug}` | Post individual |
| GET | `/author/{id}` | Perfil públic d'autor |
| GET | `/search` | Cercador de posts |

### Autenticació
| Mètode | Ruta | Descripció |
|--------|------|------------|
| GET | `/login` | Formulari de login |
| POST | `/login` | Processar login |
| GET | `/register` | Formulari de registre |
| POST | `/register` | Processar registre |
| POST | `/logout` | Tancar sessió |

### Gestió de posts (requereix autenticació)
| Mètode | Ruta | Descripció |
|--------|------|------------|
| GET | `/my-posts` | Els meus posts |
| GET | `/my-posts/create` | Formulari de creació |
| POST | `/my-posts` | Crear post |
| GET | `/my-posts/{id}/edit` | Formulari d'edició |
| POST | `/my-posts/{id}/update` | Actualitzar post |
| POST | `/my-posts/{id}/delete` | Eliminar post |
| POST | `/my-posts/{id}/publish` | Publicar / Arxivar post |

### Perfil d'usuari (requereix autenticació)
| Mètode | Ruta | Descripció |
|--------|------|------------|
| GET | `/profile` | Veure perfil |
| GET | `/profile/edit` | Editar perfil |
| POST | `/profile/update` | Actualitzar perfil |
| POST | `/profile/avatar` | Actualitzar avatar |

### Administració (requereix rol admin)
| Mètode | Ruta | Descripció |
|--------|------|------------|
| GET | `/admin` | Dashboard |
| GET | `/admin/users` | Llistat d'usuaris |
| POST | `/admin/users/{id}/delete` | Eliminar usuari |
| GET | `/admin/posts` | Llistat de posts |
| POST | `/admin/posts/{id}/status` | Canviar estat post |
| POST | `/admin/posts/{id}/delete` | Eliminar post |
| GET | `/admin/audit` | Historial d'accions |
| POST | `/admin/audit/{id}/restore` | Restaurar entitat |
| POST | `/admin/audit/{id}/delete` | Eliminar entrada historial |

---

## 🛡️ Seguretat

- **CSRF**: Tots els formularis POST inclouen un token CSRF validat al servidor
- **Prepared Statements**: Totes les consultes utilitzen PDO amb paràmetres preparats
- **Password Hashing**: Les contrasenyes es guarden amb `password_hash()` de PHP
- **Middleware**: Les rutes protegides comproven autenticació i rol abans d'executar res
- **Validació**: Totes les dades d'entrada es validen al servidor

---

## 📋 Funcionalitats destacades

### Sistema d'auditoria
El panell d'administració inclou un historial complet d'accions. Quan s'elimina un usuari, es guarda un snapshot JSON complet (incloent els seus posts) a la taula `audit_log`. Des del historial es poden restaurar usuaris eliminats — i els seus posts es restauren automàticament. Les entrades expiren als 9 mesos.

### Middleware
Quatre middleware independents gestionen el control d'accés: `AuthMiddleware`, `GuestMiddleware`, `OwnerMiddleware` i `AdminMiddleware`. Cada controller els crida explícitament al principi de cada mètode.

### Logging
Integració amb Monolog per registrar errors, avisos i esdeveniments de l'aplicació en fitxers separats (`mvcApp.log`, `mvcError.log`, `mvcWarnings.log`).

---

## 📦 Dependències

```json
{
    "require": {
        "vlucas/phpdotenv": "^5.6",
        "monolog/monolog": "^3.0"
    }
}
```
