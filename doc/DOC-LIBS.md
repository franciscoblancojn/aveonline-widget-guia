# DOC-LIBS.md

> Convenciones y uso de `franciscoblancojn/wordpress_utils` en Aveonline widget guia.

---

## Instalación

La librería vive en `libs/` (Composer vendor renombrado). **Nunca edites `libs/` a mano.**

| Comando | Propósito |
|---|---|
| `npm run install` | `composer install --no-dev` + limpieza + renombra `vendor` → `libs` |
| `npm run update` | Borra `libs/` y `composer.lock`, reinstala desde cero |

`composer.json` usa `"autoloader-suffix": "AVWG"` para que el autoloader (`ComposerAutoloaderInitAVWG`) no colisione con otros plugins que incluyen la misma librería (Generate Page AI usa `GPAI`).

---

## Clases usadas

### `FWUUpdate` — Auto-update vía GitHub

Se inicializa en `index.php`. Ver `doc/DOC-UPDATE.md`.

## Clases disponibles (no usadas aún)

`FWUPage`, `FWUTooltip`, `FWUModal`, `FWUCollapse`, `FWUExportImport`, `FWURespond`, `FWUSystemLog`, `FWUComponent`. Si se usan, importarlas con `use franciscoblancojn\wordpress_utils\{Clase};` y documentarlas aquí.

---

## Convenciones

1. **Namespace**: `franciscoblancojn\wordpress_utils\` — siempre importar con `use`.
2. **Prefix CSS**: la librería usa `fwue-`; el plugin usa `AVWG_`.
3. **No hardcodees paths**: usa `AVWG_DIR` y `AVWG_URL`.
