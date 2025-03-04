<?php

function AVWG_Component_Widget()  {
    ob_start();
    ?>
    <div class="AVWG_Component_Widget">
        <?=AVWG_Component_Form()?>
        <?=AVWG_Component_Guias()?>
    </div>
    <style>
        .AVWG_Component_Widget{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

    </style>
    <?php
    return ob_get_clean();
}