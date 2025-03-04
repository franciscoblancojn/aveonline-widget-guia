<?php

function AVWG_Component_Guia()  {
    ob_start();
    ?>
    <div class="AVWG_Component_Guia">
        <div class="AVWG_Component_Guia_top">
            <div class="AVWG_Component_Guia_origen">
                <?=$origen?>
            </div>
            <div class="AVWG_Component_Guia_destino">
                <?=$destino?>
            </div>
        </div>
        <p class="AVWG_Component_Guia_values">
            Número de guía 
            <strong><?=$Guia_Number?></strong>
        </p>
        <p class="AVWG_Component_Guia_values">
            Nombre
            <strong><?=$Guia_Name?></strong>
        </p>
        <p class="AVWG_Component_Guia_status">
            Estado: <?=$Guia_Status?>
        </p>
    </div>
   
    <?php
    return ob_get_clean();
}