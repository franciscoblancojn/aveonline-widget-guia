<?php
function AVWG_Component_Guias($settings)  {
    ob_start();
    ?>
    <div id="AVWG_Component_Guias" class="AVWG_Component_Guias">
        
    </div>
    <style>
        .AVWG_Component_Guias{
            display:grid;
            gap:1rem;
        }
        .AVWG_Component_Guia{
            display:grid;
            gap:1rem;
        }
    </style>
    <script>
        const AVWG_onGetHtmlGuia = (guia) => {
            if(!guia?.transportadora){
                return `
                    <div class="AVWG_Component_Guia">
                        <div class="AVWG_Component_Guia_numeroguia">
                            <?=$settings["guia_numeroguia"]?>: 
                            <strong>${guia?.numeroguia}</strong>
                        </div>
                        <div class="AVWG_Component_Guia_status">
                            Estado: Guía no Encontrada
                        </div>
                    </div>
                `
            }
            return `
                <div class="AVWG_Component_Guia">
                    <div class="AVWG_Component_Guia_numeroguia">
                        <?=$settings["guia_numeroguia"]?>: 
                        <strong>${guia?.numeroguia}</strong>
                    </div>
                    <?php
                        foreach ($settings['guia_items'] as $k => $value) {
                            $key = $value["key"];
                            $label = $value["label"];
                            $class = $value["class"];
                            ?>
                                <div class="AVWG_Component_Guia_item AVWG_Component_Guia_item_<?=$key?> <?=$class?>">
                                    <?=$label?>:
                                    <strong>${guia?.<?=$key?>}</strong>
                                </div>
                            <?php
                        }
                    ?>
                    <div class="AVWG_Component_Guia_status">
                        <?=$settings["guia_nombreEstadoAve"]?>: 
                        <strong>${guia?.nombreEstadoAve}</strong>
                    </div>

                </div>
            `
        }
        const AVWG_onGetGuias_callback = (guias)=>{
            const content = document.getElementById("AVWG_Component_Guias")
            const html = guias.map(AVWG_onGetHtmlGuia).join("")
            content.innerHTML = html
        }
    </script>
    <?php
    return ob_get_clean();
}