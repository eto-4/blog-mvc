# Requisits Funcionals

# COMPOSER/vendor Required
https://getcomposer.org/download/

## 1. Estructura MVC
- **Model:** Classes per gestionar les entitats i la lògica de negoci (per exemple, `Tasca`).  
- **Vista:** Plantilles PHP per a la presentació, incloent layouts (header, footer) i vistes de formularis o llistats.  
- **Controlador:** Classes que gestionen la lògica de control i les rutes corresponents (`HomeController`, `TaskController`).  

## 2. Sistema de Routing
- Router personalitzat que gestiona les rutes de l'aplicació.  
- Suport per a rutes dinàmiques amb paràmetres (ex.: `/tasques/{id}`).  
- Gestió d'errors 404.  
- Configuració automàtica del basePath.  

## 3. Operacions CRUD per a Tasques
- **CREATE:** Formularis per crear noves tasques.  
- **READ:** Llistat complet de tasques i visualització individual.  
- **UPDATE:** Formularis per editar tasques existents.  
- **DELETE:** Eliminació de tasques amb confirmació.  

## 4. Base de Dades
- Connexió a MySQL utilitzant PDO.  
- Implementació del patró Singleton per a la connexió.  
- Creació i verificació de la base de dades i de les taules de manera aïllada a l'inici de l'aplicació.  
- Taula `tasques` amb camps: `id`, `nom`.  
- Gestió d'errors amb try-catch i llançament d'excepcions.  

## 5. Sistema de Missatges Flash
- Classe `FlashMessages` per mostrar notificacions a l'usuari.  
- Suport per a diferents tipus: success, error, warning, info.  
- Integració amb Bootstrap per un estil consistent.  

## 6. Validator
- Classe `Validator` per a la validació de dades de formularis i paràmetres GET, garantint consistència a tota l'aplicació.  

## 7. Logging i Monitoreig
- Integració de logging centralitzat amb Monolog.  
- Registre d'errors i excepcions en arxius de log (`logs/mvc.log`).  
- Carpeta de logs gestionada de manera que no es pugi contingut sensible al repositori.  

---

# Requisits Tècnics

## Estructura de Carpetes
blog-mvc/.
│   .env
│   .envExample
│   .gitignore
│   composer.json
│   composer.lock
│   README.md
│
├───public
│   │   .htaccess
│   │   index.php
│   │
│   └───assets
│       ├───css
│       │       style.css
│       │
│       ├───imgs
│       └───js
├───src
│   ├───Domain
│   │   ├───Models
│   │   │       Post.php
│   │   │       User.php
│   │   │
│   │   ├───Services
│   │   │       AuthService.php
│   │   │       ValidationService.php
│   │   │
│   │   └───Slug
│   │           Slug.php
│   │           SlugGenerator.php
│   │
│   ├───Http
│   │   ├───Controllers
│   │   │       AuthController.php
│   │   │       HomeController.php
│   │   │       PostController.php
│   │   │       UserController.php
│   │   │
│   │   ├───InOutData
│   │   │   ├───Input
│   │   │   │       Sanitizer.php
│   │   │   │
│   │   │   └───Output
│   │   │           Flash.php
│   │   │
│   │   ├───Middleware
│   │   │       AuthMiddleware.php
│   │   │       GuestMiddleware.php
│   │   │       OwnerMiddleware.php
│   │   │
│   │   └───Session
│   │           Session.php
│   │
│   ├───Infrastructure
│   │   ├───Database
│   │   │   ├───DatabaseCore
│   │   │   │       Database.php
│   │   │   │
│   │   │   └───dbManager
│   │   │       │   dbManager.php
│   │   │       │
│   │   │       └───dbMigrations
│   │   │               create_database.sql
│   │   │               create_tables.sql
│   │   │
│   │   ├───Logging
│   │   │   └───logs
│   │   │           .gitkeep
│   │   │           mvcApp.log
│   │   │           mvcError.log
│   │   │           mvcWarnings.log
│   │   │
│   │   ├───Routing
│   │   │       Redirect.php
│   │   │       Router.php
│   │   │
│   │   ├───Security
│   │   │       Csrf.php
│   │   │
│   │   └───Storage
│   │       └───Uploads
│   └───Views
│       ├───auth
│       │       auth.php
│       │
│       ├───home
│       │       404.php
│       │       home.php
│       │
│       ├───layouts
│       │       app.php
│       │       footer.php
│       │       header.php
│       │
│       ├───posts
│       │       create.php
│       │       edit.php
│       │       index.php
│       │
│       └───user
└───vendor/

## Comandes per que tot funcion correctament:
1. Clonar/Descarregar el repo a un dir.
2. Obrir la terminal insitu.
3. Executar: ```composer install```
4. Afegir .env al projecte amb les dades necessaries.
5. Fi, ja pots tocar el que vulguis.

---

# Rutes de l'Estructura
- `GET /` - Pàgina principal  
- `GET /tasques` - Llistat de tasques  
- `GET /tasques/create` - Formulari de creació  
- `POST /tasques` - Processar creació  
- `GET /tasques/{id}/edit` - Formulari d'edició  
- `POST /tasques/{id}` - Processar actualització  
- `POST /tasques/{id}/delete` - Eliminar tasca  

---

# Interfície d'Usuari
- Ús de Bootstrap 5 per a l'estil.  
- Disseny responsive i modern.  
- Navegació amb breadcrumbs.  
- Confirmacions JavaScript per eliminacions.  
- Missatges flash per feedback a l'usuari.  

---

# Aspectes de Disseny
- Paleta de colors coherent.  
- Ús d'icones Bootstrap Icons.  
- Animacions CSS suaus.  
- Empty states quan no hi ha dades.  
- Footer fix al final de la pàgina.  

---

# Entregables
- Codi font complet seguint l'estructura MVC, connexió Singleton a la BBDD i validació en tots els formularis.  
- Base de dades MySQL amb la taula corresponent, verificació i creació automàtica de DB i taules.  
- Carpeta de logs amb logging d'errors i excepcions mitjançant Monolog.  
- Fitxer `.htaccess` configurat per al routing.  
- Documentació del codi amb comentaris clars.  
- Aplicació funcional amb totes les operacions CRUD.  
- Vídeo demostratiu del funcionament de l'aplicació.  