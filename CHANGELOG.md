# Changelog

> Todas las versiones del plugin Aveonline widget guia.

---

## [Unreleased]

- Rastreo de guías: cada guía se consulta también con un `0` adelante (ej. `1234` → `1234` y `01234`) y si inicia con `0` también se consulta sin él (ej. `01234` → `01234` y `1234`); se unen los resultados encontrados. "Guía no Encontrada" solo se muestra si ninguna variante existe

- Sistema de actualización migrado a `FWUUpdate` de `franciscoblancojn/wordpress_utils` (vía Composer en `libs/`, autoloader con sufijo `AVWG`), igual que Generate Page AI
- Eliminado `update.php` (updater antiguo `github_updater_plugin_wordpress`)
- Añadido `package.json` con scripts de release (`npm run push-v`), sync de versión e instalación de `libs/`
- Harness de validación del proyecto (`bin/harness.sh`) con script `npm run check`
- Añadidos `AGENTS.md`, `CONTEXT.md`, `opencode.json`, agentes en `.opencode/agents/` y skills en `.rules/`
- Añadidas guías: `doc/DOC-UPDATE.md` y `doc/DOC-LIBS.md`

## [1.2.0]

- Opción "Usar Get para Request" para buscar guías desde `?guias=`

## [1.1.2]

- Actualización del updater

## [1.1.1]

- Ajustes
