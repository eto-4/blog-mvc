# API / Rutes — Blog MVC

> Aquesta aplicació segueix el patró MVC tradicional amb formularis HTML.
> Les respostes són redireccions o vistes renderitzades, no JSON.
> Totes les rutes POST requereixen un token CSRF vàlid al body.

---

## 🌐 Públiques

### `GET /`
Pàgina principal del blog.

**Resposta:** Vista `home/home.php`

---

### `GET /posts`
Llistat de posts publicats amb paginació.

**Paràmetres GET**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `page` | int | Pàgina actual (per defecte: 1) |

**Resposta:** Vista `posts/index.php`

---

### `GET /posts/{slug}`
Mostra un post individual. Incrementa el comptador de visualitzacions.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `slug` | string | Slug únic del post |

**Resposta**
| Codi | Descripció |
|------|------------|
| 200 | Vista `posts/show.php` |
| 404 | Post no trobat o no publicat |

---

### `GET /author/{id}`
Perfil públic d'un autor amb els seus posts publicats.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `id` | int | ID de l'usuari |

**Resposta**
| Codi | Descripció |
|------|------------|
| 200 | Vista del perfil públic |
| 404 | Usuari no trobat |

---

### `GET /search`
Cerca posts per títol, contingut o resum.

**Paràmetres GET**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `q` | string | Terme de cerca |

**Resposta:** Vista `posts/index.php` amb resultats filtrats

---

## 🔐 Autenticació

### `GET /login`
Mostra el formulari de login.

> Redirigeix a `/` si l'usuari ja té sessió activa.

**Resposta:** Vista `auth/login.php`

---

### `POST /login`
Processa el login.

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `email` | string | ✓ | Email de l'usuari |
| `password` | string | ✓ | Contrasenya |
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/` si login correcte |
| 302 | Redirect a `/login` amb error si credencials incorrectes |

---

### `GET /register`
Mostra el formulari de registre.

> Redirigeix a `/` si l'usuari ja té sessió activa.

**Resposta:** Vista `auth/register.php`

---

### `POST /register`
Processa el registre d'un nou usuari.

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `name` | string | ✓ | Nom de l'usuari |
| `email` | string | ✓ | Email únic |
| `password` | string | ✓ | Mínim 8 caràcters, 1 majúscula, 1 número, 1 símbol |
| `password_confirmation` | string | ✓ | Ha de coincidir amb `password` |
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/` si registre correcte |
| 302 | Redirect a `/register` amb errors de validació |

---

### `POST /logout`
Tanca la sessió de l'usuari.

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/login` |

---

## 📝 Gestió de posts

> Totes les rutes d'aquest bloc requereixen sessió activa. Sense autenticació redirigeix a `/login`.

### `GET /my-posts`
Llistat de tots els posts de l'usuari autenticat (tots els estats).

**Resposta:** Vista `posts/my-posts.php`

---

### `GET /my-posts/create`
Mostra el formulari de creació d'un nou post.

**Resposta:** Vista `posts/create.php`

---

### `POST /my-posts`
Crea un nou post.

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `title` | string | ✓ | Títol del post (el slug es genera automàticament) |
| `content` | string | ✓ | Contingut del post |
| `excerpt` | string | — | Resum breu (es genera automàticament si s'omiteix) |
| `status` | enum | ✓ | `draft` \| `published` \| `archived` |
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/my-posts` si creació correcta |
| 302 | Redirect a `/my-posts/create` amb errors de validació |

---

### `GET /my-posts/{id}/edit`
Mostra el formulari d'edició d'un post.

> Requereix que l'usuari sigui l'autor del post.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `id` | int | ID del post |

**Resposta**
| Codi | Descripció |
|------|------------|
| 200 | Vista `posts/edit.php` |
| 302 | Redirect a `/my-posts` si no és l'autor |

---

### `POST /my-posts/{id}/update`
Actualitza un post existent.

> Requereix que l'usuari sigui l'autor del post.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `id` | int | ID del post |

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `title` | string | ✓ | Títol del post |
| `content` | string | ✓ | Contingut del post |
| `excerpt` | string | — | Resum breu |
| `status` | enum | ✓ | `draft` \| `published` \| `archived` |
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/my-posts` si actualització correcta |
| 302 | Redirect a `/my-posts/{id}/edit` amb errors de validació |

---

### `POST /my-posts/{id}/delete`
Elimina un post.

> Requereix que l'usuari sigui l'autor del post.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `id` | int | ID del post |

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/my-posts` |

---

### `POST /my-posts/{id}/publish`
Canvia l'estat d'un post entre `published` i `archived`.

> Si el post estava publicat passa a `archived`. Si estava en `draft` o `archived` passa a `published`.
> Requereix que l'usuari sigui l'autor del post.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `id` | int | ID del post |

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/my-posts` |

---

## 👤 Perfil d'usuari

> Totes les rutes d'aquest bloc requereixen sessió activa.

### `GET /profile`
Mostra el perfil de l'usuari autenticat amb estadístiques.

**Resposta:** Vista `user/profile.php`

---

### `GET /profile/edit`
Mostra el formulari d'edició del perfil.

**Resposta:** Vista `user/edit.php`

---

### `POST /profile/update`
Actualitza el nom, la biografia i opcionalment la contrasenya.

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `name` | string | ✓ | Nom de l'usuari |
| `bio` | string | — | Biografia (opcional) |
| `password` | string | — | Nova contrasenya (deixar buit per no canviar) |
| `password_confirmation` | string | — | Ha de coincidir amb `password` |
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/profile` si actualització correcta |
| 302 | Redirect a `/profile/edit` amb errors de validació |

---

### `POST /profile/avatar`
Puja una nova imatge d'avatar.

**Body (multipart/form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `avatar` | file | ✓ | Imatge (JPG, PNG, WEBP — màx. 2 MB) |
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/profile/edit` amb missatge d'èxit o error |

---

## 🛡️ Administració

> Totes les rutes d'aquest bloc requereixen sessió activa i rol `admin`.

### `GET /admin`
Dashboard amb estadístiques globals de l'aplicació.

**Resposta:** Vista `admin/index.php`

---

### `GET /admin/users`
Llistat de tots els usuaris amb estadístiques de posts i visualitzacions.

**Resposta:** Vista `admin/users.php`

---

### `POST /admin/users/{id}/delete`
Elimina un usuari i guarda un snapshot complet (usuari + posts) a l'historial.

> No es pot eliminar el propi compte d'admin.
> Els posts de l'usuari s'eliminen per CASCADE i es poden restaurar des de l'historial.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `id` | int | ID de l'usuari |

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/admin/users` |

---

### `GET /admin/posts`
Llistat de tots els posts de tots els usuaris.

**Resposta:** Vista `admin/posts.php`

---

### `POST /admin/posts/{id}/status`
Canvia l'estat d'un post.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `id` | int | ID del post |

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `status` | enum | ✓ | `draft` \| `published` \| `archived` |
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/admin/posts` |

---

### `POST /admin/posts/{id}/delete`
Elimina un post i guarda un snapshot a l'historial per poder restaurar-lo.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `id` | int | ID del post |

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/admin/posts` |

---

### `GET /admin/audit`
Historial d'accions administratives no expirades.

**Resposta:** Vista `admin/audit.php`

---

### `POST /admin/audit/{id}/restore`
Restaura l'entitat associada a una entrada de l'historial.

> Si l'entitat és un usuari, els seus posts també es restauren automàticament com a `draft`.
> Si l'entitat és un post, l'autor ha d'existir prèviament.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `id` | int | ID de l'entrada al historial |

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/admin/audit` amb missatge d'èxit o error |

---

### `POST /admin/audit/{id}/delete`
Elimina permanentment una entrada de l'historial.

> Aquesta acció és irreversible. L'entitat associada no es podrà restaurar.

**Paràmetres de ruta**
| Paràmetre | Tipus | Descripció |
|-----------|-------|------------|
| `id` | int | ID de l'entrada al historial |

**Body (form-data)**
| Camp | Tipus | Requerit | Descripció |
|------|-------|----------|------------|
| `csrf_token` | string | ✓ | Token CSRF |

**Resposta**
| Codi | Descripció |
|------|------------|
| 302 | Redirect a `/admin/audit` |
