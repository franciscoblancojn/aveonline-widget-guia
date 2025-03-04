<?php

function AVWG_Component_Widget($settings)  {
    ob_start();
    ?>
    <div class="AVWG_Component_Widget">
        <?=AVWG_Component_Form($settings)?>
        <?=AVWG_Component_Guias($settings)?>
    </div>
    <style>
        .AVWG_Component_Widget{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        @media (max-width:767px) {
            .AVWG_Component_Widget{
                grid-template-columns: 1fr;
            }
        }

    </style>
    <?php
    return ob_get_clean();
}