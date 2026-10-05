---
name: avwg-plugin
description: >-
  Use ONLY when working on Aveonline widget guia plugin features: the Elementor
  widget AVWG_AveFormGuias, its components (form, guias, widget), the Aveonline
  guide tracking API, style controls, or the FWUUpdate updater. Contains
  plugin-specific rules, class references and data flow constraints.
---

# AVWG Plugin — Reglas Específicas del Plugin

Actívala cuando trabajes con funcionalidades propias del plugin Aveonline widget guia.

---

## Widget Elementor (`src/widget.php`)

- Clase `AVWG_AveFormGuias` en namespace `Elementor`, `get_name()` = `ave_form_guias`. **No cambies el name**: rompería los widgets ya insertados en páginas.
- Los IDs de controles (`title`, `text`, `alert`, `use_get`, `label`, `placeholder`, `btn`, `guia_numeroguia`, `guia_items`, `guia_nombreEstadoAve`) se guardan en `_elementor_data`. No los renombres; si cambian, mantén compatibilidad con el valor anterior.
- Cada control de estilo se crea con `addStyleControler($key, $name, $class)`. El `$class` debe existir en el markup del componente.
- El repeater `guia_items` usa `key` (SELECT con los campos de la respuesta de la API), `label` y `class`.

## Componentes (`src/component/`)

- Cada componente es una función `AVWG_Component_*($settings)` que retorna HTML con `ob_start()` / `ob_get_clean()`.
- CSS y JS van inline en el mismo componente.
- Nuevo componente → archivo en `src/component/` + `require_once AVWG_DIR . 'src/component/{nombre}.php';` en `src/component/_.php`.

## API de guías

```
POST https://app.aveonline.co/api/comunes/v2.0/guiasNacionalP2P.php
Content-Type: application/json
{ "tipo": "infoGuiaP2PV3", "guia": "<numero>" }
```

- La respuesta útil está en `result.data[0]`; si no trae `transportadora` se muestra "Guía no Encontrada".
- `AVWG_onGetGuias_Request` siempre devuelve `numeroguia`, incluso en error.
- `AVWG_onGetGuias_callback(guias)` es el contrato entre `form.php` y `guias.php`: no lo renombres.
- Múltiples guías se separan por comas y se consultan en paralelo (`Promise.all`).

## Escape

- Settings de texto → `esc_html()`; atributos (`placeholder`, `value`, clases) → `esc_attr()`; WYSIWYG (`text`, `alert`) → `wp_kses_post()`.
- `$_GET['guias']` → `sanitize_text_field(wp_unslash(...))` + `esc_attr()`.
- Settings que se imprimen dentro de template literals JS → `esc_js()` / `wp_json_encode()`.

## Updater

- `FWUUpdate::init` en `index.php` con `path_repository` `franciscoblancojn/aveonline-widget-guia` y branch `master`.
- Ver `doc/DOC-UPDATE.md`. Valida con `npm run check`.
