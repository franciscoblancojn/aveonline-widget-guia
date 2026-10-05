# DOC-UPDATE.md

> Sistema de actualización y releases de Aveonline widget guia (mismo sistema que Generate Page AI).

---

## Cómo funciona

`index.php` carga `libs/autoload.php` e inicializa el updater de la librería `franciscoblancojn/wordpress_utils`:

```php
require_once __DIR__ . '/libs/autoload.php';

use franciscoblancojn\wordpress_utils\FWUUpdate;

FWUUpdate::init([
    'basename'          => AVWG_BASENAME,
    'dir'               => AVWG_DIR,
    'file'              => "index.php",
    'path_repository'   => 'franciscoblancojn/aveonline-widget-guia',
    'branch'            => 'master',
    'token_array_split' => [ /* token GitHub partido */ ],
]);
```

`FWUUpdate::init` (solo en admin, en `plugins.php` y `update.php?action=upgrade-plugin`):

1. Consulta `https://api.github.com/repos/{path_repository}/releases/latest` y cachea la respuesta en un transient (`github_updater_{md5}`) 1 minuto (evita el rate limit de GitHub).
2. Compara `tag_name` (sin `v`) con el `Version:` del header de `index.php`.
3. Si hay versión nueva, inyecta la actualización en `site_transient_update_plugins` con el paquete `https://github.com/{path_repository}/archive/refs/heads/{branch}.zip`.
4. Agrega el link **Actualizar** en la fila del plugin.

> Diferencia con el `update.php` antiguo: la librería cachea el release con transient y se carga vía Composer (no hay copia local de la función).

---

## Publicar una versión

1. Actualiza `CHANGELOG.md` (renombra `[Unreleased]` a la nueva versión).
2. `npm run check` → `FAILURES: 0`.
3. `npm run push-v -- patch` (o `minor` / `major`):
   - sube `Version:` en `index.php`,
   - sincroniza `package.json` y `Stable tag` de `Readme.md`,
   - `git commit -m "Release X.Y.Z"`, `git tag X.Y.Z`, push de `master` y del tag.
4. Crea el **Release** en GitHub para el tag (el updater lee `releases/latest`, no los tags).

---

## Notas

- El paquete descargado es la rama `master`, por lo que `libs/` **debe estar commiteado**.
- No cambies `autoloader-suffix` (`AVWG`) en `composer.json`: si dos plugins comparten sufijo, el segundo provoca "Cannot declare class ComposerAutoloaderInit...".
