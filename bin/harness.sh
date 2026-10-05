#!/bin/bash
#
# Harness de validación del proyecto Aveonline widget guia.
#
# Uso:
#   bin/harness.sh                 → Ejecuta todas las validaciones.
#   bin/harness.sh doc             → Lista y valida los docs en doc/.
#   bin/harness.sh doc <Nombre>    → Crea un doc en doc/ si no existe.
#
# Validaciones:
#   1. php -l en todo el plugin (excluye libs/).
#   2. Sintaxis PHP <= 7.0 (no ?->, match, readonly, arrow, named args).
#   3. JavaScript ES5 en archivos .js (el JS inline en componentes PHP puede usar ES6+).
#   4. Prefijo CSS AVWG_ en clases de los componentes.
#   5. Funciones/clases requeridas presentes (prefijo AVWG_).
#   6. require_once declarados en cargadores → archivo existe.
#   7. Sistema de actualización (FWUUpdate vía libs/ con autoloader AVWG).
#   8. Versión sincronizada (index.php, package.json, Readme.md).
#   9. composer validate.
#  10. Referencias a documentación → archivo existe en doc/.
#
# NO ejecutar cambios de git. Solo validaciones de lectura + creación de docs.

set -u

PLUGIN_DIR="$(cd "$(dirname "$0")/.." && pwd)"
DOC_DIR="$PLUGIN_DIR/doc"
LIB_DIR="$PLUGIN_DIR/libs"
FAILURES=0
WARNINGS=0

fail() {
    printf '  [FAIL] %s\n' "$*"
    FAILURES=$((FAILURES + 1))
}

warn() {
    printf '  [WARN] %s\n' "$*"
    WARNINGS=$((WARNINGS + 1))
}

ok() {
    printf '  [ OK ] %s\n' "$*"
}

# ---------------------------------------------------------------
# Helpers de detección de sintaxis moderna (PHP 7.1+ / 8.x)
# ---------------------------------------------------------------
has_modern_php_syntax() {
    local file="$1"

    # Nullsafe operator ?-> (PHP 8.0)
    if grep -qE '\?->' "$file"; then
        echo 'operador nullsafe ?-> (PHP 8.0)'
        return 0
    fi
    # match expression (PHP 8.0)
    if grep -qE '\bmatch[[:space:]]*\(' "$file"; then
        echo 'expresión match (PHP 8.0)'
        return 0
    fi
    # readonly properties (PHP 8.1)
    if grep -qE '\breadonly[[:space:]]+' "$file"; then
        echo 'propiedad readonly (PHP 8.1)'
        return 0
    fi
    # Arrow functions fn() (PHP 7.4)
    if grep -qE '\bfn[[:space:]]*\([^)]*\)[[:space:]]*=>' "$file"; then
        echo 'arrow function fn() (PHP 7.4)'
        return 0
    fi
    # Typed properties (PHP 7.4)
    if grep -qE '(public|private|protected)([[:space:]]+static)?[[:space:]]+\??[A-Za-z_\\][A-Za-z0-9_\\]*[[:space:]]+\$' "$file"; then
        echo 'propiedad tipada (PHP 7.4)'
        return 0
    fi
    # Enums (PHP 8.1)
    if grep -qE '^[[:space:]]*enum[[:space:]]+[A-Za-z_]' "$file"; then
        echo 'enum (PHP 8.1)'
        return 0
    fi

    return 1
}

has_modern_js_syntax() {
    local file="$1"

    # Arrow functions => (ES6)
    if grep -qE '=>' "$file"; then
        echo 'arrow function o =>'
        return 0
    fi
    # Template literals con backtick
    if grep -q '`' "$file"; then
        echo 'template literal (backtick)'
        return 0
    fi
    # let/const (ES6)
    if grep -qE '\b(let|const)[[:space:]]+[a-zA-Z_$]' "$file"; then
        echo 'let/const (ES6)'
        return 0
    fi

    return 1
}

# ---------------------------------------------------------------
# Comando: crear un doc en doc/
# ---------------------------------------------------------------
create_doc() {
    local name="${1:-}"
    if [ -z "$name" ]; then
        printf 'Uso: bin/harness.sh doc <Nombre>\n'
        printf '     bin/harness.sh doc\n'
        return 1
    fi

    if [ "${name##*.}" != "md" ]; then
        name="$name.md"
    fi
    if ! echo "$name" | grep -q '^DOC-'; then
        name="DOC-$name"
    fi

    local file="$DOC_DIR/$name"
    if [ -f "$file" ]; then
        printf 'Ya existe: %s\n' "$file"
        return 0
    fi

    mkdir -p "$DOC_DIR"
    cat > "$file" <<EOF
# $name

> Documentación de Aveonline widget guia. Se sirve de la carpeta doc/.

---

## Descripción

_Escribir aquí._

---

## Uso

_Escribir aquí._
EOF

    printf 'Creado: %s\n' "$file"
    return 0
}

# ---------------------------------------------------------------
# Comando: listar/validar docs en doc/
# ---------------------------------------------------------------
list_docs() {
    printf '\nDocumentación en %s:\n' "$DOC_DIR"

    if [ ! -d "$DOC_DIR" ]; then
        warn "falta la carpeta $DOC_DIR"
        return 1
    fi

    local docs
    docs="$(find "$DOC_DIR" -name '*.md' -type f | sort)"

    if [ -z "$docs" ]; then
        warn "no hay archivos .md en $DOC_DIR"
        return 1
    fi

    while read -r d; do
        ok "${d#$PLUGIN_DIR/}"
    done <<< "$docs"

    return 0
}

# ---------------------------------------------------------------
# Validación de referencias a documentación
# ---------------------------------------------------------------
validate_doc_refs() {
    printf '\n---- Referencias a documentación (doc/) ----\n'

    if [ ! -d "$DOC_DIR" ]; then
        fail "no existe la carpeta de documentación: $DOC_DIR"
        return
    fi

    local refs
    refs="$(grep -rhoE 'DOC-[A-Za-z0-9_.-]+\.md' \
        "$PLUGIN_DIR/AGENTS.md" "$PLUGIN_DIR/CONTEXT.md" \
        "$PLUGIN_DIR/Readme.md" "$PLUGIN_DIR/CHANGELOG.md" "$PLUGIN_DIR/.rules" 2>/dev/null \
        | sort -u)"

    if [ -z "$refs" ]; then
        warn 'no se encontraron referencias a DOC-*.md'
        return
    fi

    while read -r ref; do
        if [ -f "$DOC_DIR/$ref" ]; then
            ok "$ref -> existe en doc/"
        else
            fail "$ref referenciado pero no existe en doc/$ref"
        fi
    done <<< "$refs"
}

# ---------------------------------------------------------------
# Validaciones estándar del proyecto
# ---------------------------------------------------------------
php_files() {
    { find "$PLUGIN_DIR/src" -name '*.php' -type f 2>/dev/null; echo "$PLUGIN_DIR/index.php"; } | sort
}

run_lint() {
    printf '\n---- php -l (excluyendo libs/) ----\n'

    if ! command -v php >/dev/null 2>&1; then
        warn 'php no está disponible: se omite php -l'
        return
    fi

    local f
    while read -r f; do
        if php -l "$f" >/dev/null 2>&1; then
            ok "${f#$PLUGIN_DIR/}"
        else
            php -l "$f"
            fail "${f#$PLUGIN_DIR/} no compila"
        fi
    done <<< "$(php_files)"
}

run_php_syntax() {
    printf '\n---- Sintaxis PHP <= 7.0 ----\n'

    local f why before="$FAILURES"
    while read -r f; do
        if why="$(has_modern_php_syntax "$f")"; then
            fail "${f#$PLUGIN_DIR/}: $why"
        fi
    done <<< "$(php_files)"
    [ "$FAILURES" -eq "$before" ] && ok 'sin sintaxis PHP moderna'
}

run_js_syntax() {
    printf '\n---- JavaScript ES5 (archivos .js) ----\n'

    local files f why
    files="$(find "$PLUGIN_DIR/src" -name '*.js' -type f 2>/dev/null | sort)"

    if [ -z "$files" ]; then
        ok 'sin archivos .js (el JS inline de src/component/ puede usar ES6+)'
        return
    fi

    while read -r f; do
        if why="$(has_modern_js_syntax "$f")"; then
            fail "${f#$PLUGIN_DIR/}: $why"
        fi
    done <<< "$files"
}

run_css_prefixes() {
    printf '\n---- Prefijo CSS AVWG_ en componentes ----\n'

    local files f bad before="$FAILURES"
    files="$(find "$PLUGIN_DIR/src" \( -name '*.php' -o -name '*.css' \) -type f 2>/dev/null | sort)"

    while read -r f; do
        # Atributos class="..." estáticos: cada clase debe empezar por AVWG_
        # (se ignoran valores dinámicos <?= ?> y la clase de estado "loader").
        bad="$(grep -noE 'class="[^"]*"' "$f" \
            | sed -E 's/<\?=[^?]*\?>//g; s/\$\{[^}]*\}//g' \
            | awk -F'class="' '{
                split($2, parts, "\""); n = split(parts[1], cls, /[[:space:]]+/);
                for (i = 1; i <= n; i++) if (cls[i] != "" && cls[i] !~ /^AVWG_/ && cls[i] != "loader") print $1 cls[i];
              }')"
        # Selectores de clase en bloques <style>/CSS
        bad="$(printf '%s\n%s' "$bad" "$(grep -nE '^[[:space:]]*\.[A-Za-z_-]+[^{;]*\{' "$f" | grep -vE '^[0-9]+:[[:space:]]*\.AVWG_')" | sed '/^$/d')"
        if [ -n "$bad" ]; then
            fail "${f#$PLUGIN_DIR/} tiene clases/selectores sin prefijo AVWG_"
            while read -r b; do printf '         %s\n' "$b"; done <<< "$bad"
        fi
    done <<< "$files"
    [ "$FAILURES" -eq "$before" ] && ok 'todas las clases usan prefijo AVWG_'
}

run_required_symbols() {
    printf '\n---- Funciones/clases requeridas (prefijo AVWG_) ----\n'

    local required=(
        'class AVWG_AveFormGuias'
        'function AVWG_Component_Form'
        'function AVWG_Component_Guias'
        'function AVWG_Component_Widget'
        'function AVWG_register_AveFormGuias'
    )

    local sym
    for sym in "${required[@]}"; do
        if grep -rqF "$sym" "$PLUGIN_DIR/src" "$PLUGIN_DIR/index.php"; then
            ok "$sym"
        else
            fail "falta: $sym"
        fi
    done

    if grep -rq '_register_controls()' "$PLUGIN_DIR/src"; then
        warn 'src/widget.php usa _register_controls() (deprecado en Elementor 3.1+, usar register_controls())'
    fi
}

run_requires() {
    printf '\n---- require_once en cargadores (index.php, src/**/_.php) ----\n'

    local loaders loader targets t
    loaders="$( { echo "$PLUGIN_DIR/index.php"; find "$PLUGIN_DIR/src" -name '_.php' -type f 2>/dev/null; } | sort)"

    while read -r loader; do
        targets="$(grep -oE "require_once[[:space:]]+AVWG_DIR[[:space:]]*\.[[:space:]]*'[^']+'" "$loader" | sed -E "s/.*'([^']+)'.*/\1/")"

        if [ -z "$targets" ]; then
            warn "${loader#$PLUGIN_DIR/}: sin require_once con AVWG_DIR"
            continue
        fi

        while read -r t; do
            if [ -f "$PLUGIN_DIR/$t" ]; then
                ok "$t"
            else
                fail "${loader#$PLUGIN_DIR/}: objetivo inexistente $t"
            fi
        done <<< "$targets"
    done <<< "$loaders"

    # Todo PHP de src/ debe estar cargado desde algún cargador
    local f rel
    while read -r f; do
        rel="${f#$PLUGIN_DIR/}"
        case "$rel" in */_.php) continue ;; esac
        if ! grep -rqF "'$rel'" "$PLUGIN_DIR/index.php" "$PLUGIN_DIR/src"; then
            warn "$rel no se carga desde ningún cargador"
        fi
    done <<< "$(find "$PLUGIN_DIR/src" -name '*.php' -type f | sort)"
}

run_updater() {
    printf '\n---- Sistema de actualización (FWUUpdate) ----\n'

    local index="$PLUGIN_DIR/index.php"

    if [ -f "$LIB_DIR/autoload.php" ]; then
        ok 'libs/autoload.php existe'
    else
        fail 'falta libs/autoload.php (ejecutar npm run install)'
    fi

    if [ -f "$LIB_DIR/franciscoblancojn/wordpress_utils/src/FWUUpdate.php" ]; then
        ok 'libs/.../FWUUpdate.php existe'
    else
        fail 'falta FWUUpdate.php en libs/ (ejecutar npm run install)'
    fi

    if grep -q "require_once __DIR__ . '/libs/autoload.php'" "$index"; then
        ok 'index.php carga libs/autoload.php'
    else
        fail 'index.php no carga libs/autoload.php'
    fi

    if grep -q 'FWUUpdate::init' "$index" && grep -q 'use franciscoblancojn\\wordpress_utils\\FWUUpdate;' "$index"; then
        ok 'index.php inicializa FWUUpdate::init'
    else
        fail 'index.php no inicializa FWUUpdate::init'
    fi

    if grep -q "'path_repository'[[:space:]]*=>[[:space:]]*'franciscoblancojn/aveonline-widget-guia'" "$index"; then
        ok 'path_repository apunta a franciscoblancojn/aveonline-widget-guia'
    else
        fail 'path_repository no apunta a franciscoblancojn/aveonline-widget-guia'
    fi

    if [ -f "$PLUGIN_DIR/update.php" ] || grep -rq 'github_updater_plugin_wordpress' "$index" "$PLUGIN_DIR/src"; then
        fail 'quedan restos del updater antiguo (update.php / github_updater_plugin_wordpress)'
    else
        ok 'sin updater antiguo'
    fi

    # El sufijo del autoloader debe ser propio para no chocar con otros plugins
    # que usan la misma librería (p. ej. generate-page-ai → GPAI).
    if grep -q 'ComposerAutoloaderInitAVWG' "$LIB_DIR/autoload.php" 2>/dev/null; then
        ok 'autoloader con sufijo AVWG'
    else
        fail 'libs/autoload.php no usa el sufijo AVWG (revisar composer.json → autoloader-suffix y npm run update)'
    fi
}

run_version() {
    printf '\n---- Versión sincronizada ----\n'

    local v_php v_pkg v_readme
    v_php="$(grep -i 'Version:' "$PLUGIN_DIR/index.php" | head -1 | sed 's/.*Version:[[:space:]]*//' | tr -d '[:space:]')"
    v_pkg="$(grep -E '"version"' "$PLUGIN_DIR/package.json" | head -1 | sed -E 's/.*"version":[[:space:]]*"([^"]+)".*/\1/')"
    v_readme="$(grep -E 'Stable tag:' "$PLUGIN_DIR/Readme.md" | head -1 | sed -E 's/.*Stable tag:[[:space:]]*//' | tr -d '[:space:]')"

    if [ "$v_php" = "$v_pkg" ] && [ "$v_php" = "$v_readme" ]; then
        ok "versión $v_php (index.php = package.json = Readme.md)"
    else
        fail "versiones distintas: index.php=$v_php package.json=$v_pkg Readme.md=$v_readme (npm run sync:version)"
    fi

    if grep -qF "[$v_php]" "$PLUGIN_DIR/CHANGELOG.md" 2>/dev/null; then
        ok "CHANGELOG.md tiene entrada [$v_php]"
    else
        warn "CHANGELOG.md sin entrada para [$v_php]"
    fi
}

run_composer() {
    printf '\n---- composer validate ----\n'

    if [ -f "$PLUGIN_DIR/composer.json" ] && command -v composer >/dev/null 2>&1; then
        if composer validate --no-check-publish --no-check-lock "$PLUGIN_DIR/composer.json" >/dev/null 2>&1; then
            ok 'composer.json válido'
        else
            fail 'composer.json inválido'
        fi
    else
        warn 'composer no disponible: se omite composer validate'
    fi
}

# ---------------------------------------------------------------
# Modo de ejecución
# ---------------------------------------------------------------
case "${1:-}" in
    doc)
        if [ $# -ge 2 ]; then
            create_doc "$2"
        else
            list_docs
        fi
        ;;
    *)
        run_lint
        run_php_syntax
        run_js_syntax
        run_css_prefixes
        run_required_symbols
        run_requires
        run_updater
        run_version
        run_composer
        validate_doc_refs
        ;;
esac

printf '\n%s\n' '-----------------------------'
printf '  FAILURES: %d | WARNINGS: %d\n' "$FAILURES" "$WARNINGS"
printf '%s\n' '-----------------------------'

if [ "$FAILURES" -gt 0 ]; then
    exit 1
fi
exit 0
