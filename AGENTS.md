# Aveonline widget guia — Reglas para IAs

Este archivo contiene las reglas, validaciones y convenciones que toda IA debe seguir al programar en este proyecto.

Antes de terminar cualquier tarea ejecuta **`npm run check`** (`bin/harness.sh`) y deja el resultado en `FAILURES: 0`.

---

## 1. Estándares de Código

### PHP
- **WordPress Coding Standards**: Sigue los estándares de codificación de WordPress para PHP.
- **PHP 7.0+**: No uses sintaxis moderna de PHP (nullsafe `?->`, named arguments, `match`, `readonly`, typed properties, arrow functions `fn`). El operador `??` y `<?=` están permitidos.
- **Nombrado**: Funciones y clases con prefijo `AVWG_` (ej: `AVWG_Component_Form`, `AVWG_AveFormGuias`).
- **Escape**: Toda salida de settings de Elementor o de `$_GET` debe escaparse con `esc_html()`, `esc_attr()`, `esc_url()` o `wp_kses_post()` (para campos WYSIWYG) según contexto.
- **Entrada**: `$_GET['guias']` y cualquier input deben pasar por `sanitize_text_field()` / `wp_unslash()`.

### JavaScript
- El JS vive **inline** en los componentes PHP (`src/component/*.php`) y puede usar ES6+ (async/await, template literals, arrow functions), ya que se ejecuta en el frontend moderno.
- Si se crean archivos `.js` en `src/`, deben ser **ES5** (lo valida el harness).
- Funciones globales con prefijo `AVWG_` (ej: `AVWG_onGetGuias`, `AVWG_onGetGuias_callback`).
- Al interpolar datos de la API dentro de template literals que terminan en `innerHTML`, escápalos para evitar XSS.

### CSS
- **Prefijo**: Todas las clases CSS deben llevar prefijo `AVWG_` (ej: `AVWG_Component_Form_title`). Única excepción: la clase de estado `loader`.
- Las clases que Elementor estiliza desde `addStyleControler()` en `src/widget.php` deben coincidir con las clases del markup.

---

## 2. Arquitectura del Plugin

Ver `CONTEXT.md` para el detalle.

- `index.php` → Plugin header, constantes, autoload de `libs/`, `FWUUpdate::init` y registro del widget. No agregues lógica de negocio aquí.
- `src/widget.php` → Clase `AVWG_AveFormGuias` (widget Elementor). Se carga solo en `elementor/widgets/register`.
- `src/component/_.php` → Cargador de componentes. Todo componente nuevo debe ser `require_once AVWG_DIR . '...'` desde aquí.
- `src/component/` → Componentes que devuelven HTML (`ob_start()` / `ob_get_clean()`).
- `libs/` → Vendor de Composer (`franciscoblancojn/wordpress_utils`). **Nunca editar a mano.** Ver `doc/DOC-LIBS.md`.

### Constantes
Usa las constantes definidas en `index.php`: `AVWG_KEY`, `AVWG_SLUG`, `AVWG_DIR`, `AVWG_URL`, `AVWG_BASENAME`. Nunca hardcodees paths ni URLs del plugin.

---

## 3. Sistema de actualización

- El plugin se actualiza desde GitHub con `FWUUpdate::init([...])` de `franciscoblancojn/wordpress_utils` (igual que Generate Page AI). Ver `doc/DOC-UPDATE.md`.
- `composer.json` define `"autoloader-suffix": "AVWG"`: **no lo cambies**, evita colisiones de autoloader con otros plugins que usan la misma librería.
- Los releases se publican con `npm run push-v -- major|minor|patch` (bump de versión + tag + push). Solo bajo pedido explícito del usuario.

---

## 4. Seguridad

1. **Nunca** agregues nuevos tokens o API keys en el código.
2. **Siempre** escapa salida y sanitiza entrada (ver sección 1).
3. La consulta de guías se hace desde el navegador contra `https://app.aveonline.co/api/comunes/v2.0/guiasNacionalP2P.php` (`tipo: infoGuiaP2PV3`). No cambies el endpoint sin autorización.

---

## 5. Git Workflow

1. **Commits**: No hacer commits automáticamente, solo dar sugerencias de commits.
2. Actualiza `CHANGELOG.md` con cada cambio funcional.

---

## 6. Lo que NO debes hacer

- ✗ NO modifiques el plugin header de `index.php` (salvo la versión vía `npm run push-v`).
- ✗ NO elimines el prefijo `AVWG_` de ninguna clase/función/clase CSS.
- ✗ NO agregues dependencias npm/composer sin autorización explícita.
- ✗ NO edites archivos en `libs/`.
- ✗ NO reintroduzcas `update.php` / `github_updater_plugin_wordpress` (updater antiguo).
- ✗ NO hardcodees URLs o paths — usa `AVWG_URL`, `AVWG_DIR`.
- ✗ NO añadas archivos PHP sin `require_once` desde `index.php` o `src/*/_.php`.
