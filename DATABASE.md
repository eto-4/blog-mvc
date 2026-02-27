# Base de Dades — Blog MVC

## 📊 Esquema General

L'aplicació utilitza **MySQL** amb **PDO** i el patró **Singleton** per a la connexió. La base de dades conté tres taules principals amb relacions entre elles.

---

## 📋 Taules

### `users`

Emmagatzema els usuaris registrats de l'aplicació.

| Camp | Tipus | Nullable | Default | Descripció |
|------|-------|----------|---------|------------|
| `id` | INT AUTO_INCREMENT | No | — | Clau primària |
| `name` | VARCHAR(100) | No | — | Nom de l'usuari |
| `email` | VARCHAR(150) | No | — | Email únic |
| `password` | VARCHAR(255) | No | — | Hash de la contrasenya (`password_hash()`) |
| `avatar` | VARCHAR(255) | Sí | NULL | Nom del fitxer d'avatar |
| `bio` | TEXT | Sí | NULL | Biografia de l'usuari |
| `role` | ENUM('user', 'admin') | No | `user` | Rol de l'usuari |
| `email_verified_at` | TIMESTAMP | Sí | NULL | Data de verificació d'email |
| `last_login_at` | TIMESTAMP | Sí | NULL | Últim login |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | Data de creació |
| `updated_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | Data d'última modificació |

**Índexos**
| Índex | Camp | Tipus |
|-------|------|-------|
| `PRIMARY` | `id` | Primary |
| `idx_email` | `email` | Unique |

---

### `posts`

Emmagatzema els posts del blog amb el seu contingut i estat.

| Camp | Tipus | Nullable | Default | Descripció |
|------|-------|----------|---------|------------|
| `id` | INT AUTO_INCREMENT | No | — | Clau primària |
| `title` | VARCHAR(255) | No | — | Títol del post |
| `slug` | VARCHAR(255) | No | — | Slug únic generat automàticament |
| `content` | TEXT | No | — | Contingut complet del post |
| `excerpt` | VARCHAR(500) | Sí | NULL | Resum breu (generat automàticament si s'omiteix) |
| `featured_image` | VARCHAR(255) | Sí | NULL | Imatge destacada |
| `author_id` | INT | No | — | FK → `users.id` |
| `status` | ENUM('draft', 'published', 'archived') | No | `published` | Estat del post |
| `views_count` | INT | No | 0 | Comptador de visualitzacions |
| `published_at` | TIMESTAMP | Sí | NULL | Data de publicació |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | Data de creació |
| `updated_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | Data d'última modificació |

**Índexos**
| Índex | Camp | Tipus |
|-------|------|-------|
| `PRIMARY` | `id` | Primary |
| `idx_slug` | `slug` | Unique |
| `idx_author` | `author_id` | Index |
| `idx_status` | `status` | Index |
| `idx_published_at` | `published_at` | Index |
| `idx_search` | `title`, `content` | FULLTEXT |

---

### `audit_log`

Registre d'accions administratives. Guarda un snapshot JSON de l'entitat afectada per permetre la restauració posterior.

| Camp | Tipus | Nullable | Default | Descripció |
|------|-------|----------|---------|------------|
| `id` | INT AUTO_INCREMENT | No | — | Clau primària |
| `action` | VARCHAR(50) | No | — | Tipus d'acció realitzada |
| `entity_type` | VARCHAR(50) | No | — | Tipus d'entitat afectada (`user` \| `post`) |
| `entity_id` | INT | No | — | ID de l'entitat afectada |
| `entity_data` | JSON | No | — | Snapshot complet de l'entitat en el moment de l'acció |
| `performed_by` | INT | No | — | FK → `users.id` (admin que ha fet l'acció) |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | Data de l'acció |
| `expires_at` | TIMESTAMP (GENERATED) | No | — | Data d'expiració (calculada: `created_at + 270 dies`) |

**Índexos**
| Índex | Camp | Tipus |
|-------|------|-------|
| `PRIMARY` | `id` | Primary |
| `idx_action` | `action` | Index |
| `idx_entity_type` | `entity_type` | Index |
| `idx_performed_by` | `performed_by` | Index |
| `idx_expires_at` | `expires_at` | Index |

**Valors possibles del camp `action`**
| Valor | Descripció |
|-------|------------|
| `delete_user` | Usuari eliminat per un admin |
| `delete_post` | Post eliminat per un admin |
| `switch_post_status` | Estat d'un post canviat per un admin |
| `restore_user` | Usuari restaurat des de l'historial |
| `restore_post` | Post restaurat des de l'historial |

---

## 🔗 Relacions

```
users (1) ──────────── (N) posts
         author_id → users.id
         ON DELETE CASCADE

users (1) ──────────── (N) audit_log
         performed_by → users.id
         ON DELETE CASCADE
```

### `users` → `posts`
- Relació **1 a N** — un usuari pot tenir múltiples posts
- `posts.author_id` referencia `users.id`
- **ON DELETE CASCADE** — si s'elimina un usuari, tots els seus posts s'eliminen automàticament
- Abans de l'eliminació, el sistema guarda un snapshot JSON de l'usuari i els seus posts a `audit_log` per permetre la restauració

### `users` → `audit_log`
- Relació **1 a N** — un admin pot tenir múltiples entrades al historial
- `audit_log.performed_by` referencia `users.id`
- **ON DELETE CASCADE** — si s'elimina l'admin que va fer l'acció, les seves entrades al historial també s'eliminen

---

## ⏱️ Expiració automàtica del historial

El camp `expires_at` de `audit_log` és una columna **GENERATED** calculada automàticament per MySQL:

```sql
expires_at TIMESTAMP GENERATED ALWAYS AS
    (DATE_ADD(created_at, INTERVAL 270 DAY)) STORED
```

Les entrades expiren als **9 mesos** (270 dies). La consulta de `getAuditLog()` filtra automàticament les entrades expirades:

```sql
WHERE expires_at > NOW()
```

Per eliminar entrades expirades manualment es pot cridar `purgeExpired()` al model `Admin`.

---

## 🌱 Seeder

El seeder genera dades de prova per facilitar el desenvolupament:

- **5 usuaris** (1 admin + 4 usuaris normals)
- **20 posts** amb estats variats (majoritàriament `published`)

**Credencials**
| Usuari | Email | Contrasenya | Rol |
|--------|-------|-------------|-----|
| Admin | `admin@blog.com` | `Admin123!` | admin |
| John | `john@blog.com` | `User123!` | user |

**Execució**
```bash
php src/Infrastructure/Database/Seeders/DatabaseSeeder.php
```

> ⚠️ El seeder esborra totes les dades existents abans d'inserir les noves.
