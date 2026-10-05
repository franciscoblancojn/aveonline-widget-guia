# Aveonline widget guia — Contexto para IAs

> Plugin WordPress v1.2.0 — Contexto rápido de arquitectura.

---

## ¿Qué hace este plugin?

Agrega un widget de **Elementor** ("Aveonline Formulario Guias") para rastrear guías de Aveonline:
- Formulario con input de número(s) de guía (varias separadas por comas).
- Consulta cada guía a la API pública de Aveonline desde el navegador.
- Muestra los datos de cada guía según los campos configurados en el widget (repeater).
- Opción "Usar Get para Request": precarga el input con `?guias=...` y busca automáticamente.
- Auto-update vía GitHub (`FWUUpdate`).

**Requiere**: Elementor.

---

## Constantes globales

| Constante | Valor | Uso |
|---|---|---|
| `AVWG_KEY` | `'AVWG'` | Prefijo general |
| `AVWG_SLUG` | `'aveonline-widget-guia'` | Slug del plugin |
| `AVWG_LOG` | `false` | Flag de logs |
| `AVWG_DIR` | `plugin_dir_path(__FILE__)` | Base del plugin |
| `AVWG_URL` | `plugin_dir_url(__FILE__)` | URL base |
| `AVWG_BASENAME` | `plugin_basename(__FILE__)` | Basename (usado por el updater) |

---

## Estructura de archivos

```
index.php                 → Header, constantes, libs/autoload.php, FWUUpdate::init, registro del widget
libs/                     → Composer vendor (franciscoblancojn/wordpress_utils), autoloader sufijo AVWG
src/
  widget.php              → AVWG_AveFormGuias (Elementor\Widget_Base): controles de contenido y estilo
  component/
    _.php                 → Cargador de componentes
    widget.php            → AVWG_Component_Widget($settings): layout grid (form + guías)
    form.php              → AVWG_Component_Form($settings): formulario + JS de consulta a la API
    guias.php             → AVWG_Component_Guias($settings): contenedor de resultados + render JS
bin/harness.sh            → Validaciones del proyecto (npm run check)
scripts/bump-version.sh   → Bump de versión + release (npm run push-v)
doc/                      → Documentación (DOC-*.md)
```

---

## Flujo

1. `elementor/widgets/register` → `AVWG_register_AveFormGuias()` carga `src/widget.php` y registra el widget.
2. `AVWG_AveFormGuias::render()` → `AVWG_Component_Widget($settings)`.
3. Click en el botón (o carga con `?guias=` si `use_get`) → `AVWG_onGetGuias()`:
   - separa por comas y por cada guía llama `AVWG_onGetGuia(guia)`,
   - `AVWG_onGetGuia` consulta la guía y su variante con `0` adelante (si no inicia con `0`) o sin el primer `0` (si inicia con `0`) vía `AVWG_onGetGuias_Request` (`POST` JSON `{tipo: "infoGuiaP2PV3", guia}`) y une las variantes encontradas; si ninguna existe devuelve `[{numeroguia}]` (→ "Guía no Encontrada"),
   - invoca `AVWG_onGetGuias_callback(guias)` definido en `guias.php`.
4. `AVWG_onGetHtmlGuia(guia)` arma el HTML por guía con los items del repeater `guia_items` (clave → `guia[key]`).

---

## Controles del widget (`src/widget.php`)

| Sección | Controles |
|---|---|
| Contenido | `title`, `text` (WYSIWYG), `alert` (WYSIWYG) |
| Formulario | `use_get`, `label`, `placeholder`, `btn` |
| Guía | `guia_numeroguia`, `guia_items` (repeater: `label`, `key`, `class`), `guia_nombreEstadoAve` |
| Estilo | `addStyleControler($key, $name, $class)` para cada clase `AVWG_Component_*` |

---

## Clases CSS

`AVWG_Component_Widget`, `AVWG_Component_Form`, `AVWG_Component_Form_title`, `AVWG_Component_Form_alert`, `AVWG_Component_Form_text`, `AVWG_Component_Form_label`, `AVWG_Component_Form_input`, `AVWG_Component_Form_content_btn`, `AVWG_Component_Form_btn` (+ estado `loader`), `AVWG_Component_Guias`, `AVWG_Component_Guia`, `AVWG_Component_Guia_numeroguia`, `AVWG_Component_Guia_item`, `AVWG_Component_Guia_item_{key}`, `AVWG_Component_Guia_status`.

---

## Comandos npm

| Comando | Descripción |
|---|---|
| `npm run check` | Ejecuta `bin/harness.sh` |
| `npm run install` | `composer install` → `libs/` |
| `npm run update` | Reinstala `libs/` desde cero |
| `npm run v` | Muestra la versión de `index.php` |
| `npm run sync:version` | Sincroniza versión en `package.json` y `Readme.md` |
| `npm run push-v -- major\|minor\|patch` | Bump + commit "Release X" + tag + push |

Ver `doc/DOC-UPDATE.md` y `doc/DOC-LIBS.md`.
