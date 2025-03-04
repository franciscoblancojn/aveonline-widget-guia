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
                        <div class="AVWG_Component_Guia_values">
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
                    <div class="AVWG_Component_Guia_top">
                        <div class="AVWG_Component_Guia_origen">
                            <?=$settings["guia_origen"]?>: 
                            <strong>
                                ${guia?.origen}
                                ${guia?.direccion}
                           </strong>
                        </div>
                        <div class="AVWG_Component_Guia_destino">
                            <?=$settings["guia_destino"]?>: 
                            <strong>
                                ${guia?.destino_destinatario}
                                ${guia?.barrio_destinatario}
                                ${guia?.direccion_destinatario}
                           </strong>
                        </div>
                    </div>
                    <div class="AVWG_Component_Guia_numeroguia">
                        <?=$settings["guia_numeroguia"]?>: 
                        <strong>${guia?.numeroguia}</strong>
                    </div>
                    <div class="AVWG_Component_Guia_destinatario">
                        <?=$settings["guia_destinatario"]?>: 
                        <strong>${guia?.destinatario}</strong>
                    </div>
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