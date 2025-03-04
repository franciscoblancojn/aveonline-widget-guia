<?php

function AVWG_Component_Form()  {
    ob_start();
    ?>
    <div class="AVWG_Component_Form">
        <h2 class="AVWG_Component_Form_title">
            <?=($title || "Rastrea tu guía")?>
        </h2>
        <p class="AVWG_Component_Form_alert">
            <?=($alert || "Hola, recuerda que puedes rastrear múltiples guías, separándolas por comas.")?>
        </p>
        <p class="AVWG_Component_Form_text">
            <?=($text || "En caso de salir alguna NOVEDAD, debes comunicarte directamente con la tienda en donde hiciste la compra, pues son ellos quienes deben resolverla, para que tu pedido llegue pronto.")?>
        </p>
        <label>
            <div class="AVWG_Component_Form_label">
                <?=($label || "Número de guía")?>
            </div>
            <input
                type="text"
                placeholder="<?=($placeholder || "Número de guía")?>"
                class="AVWG_Component_Form_input"
            />
        </label>
        <button>
            <?=($btn || "Buscar")?>
        </button>
    </div>
   
    <?php
    return ob_get_clean();
}