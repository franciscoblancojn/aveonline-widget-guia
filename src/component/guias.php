<?php
function AVWG_Component_Guias($settings)  {
    ob_start();
    ?>
    <div id="AVWG_Component_Guias" class="AVWG_Component_Guias">
        
    </div>
    <script>
        const AVWG_onGetHtmlGuia = (guia) => {
            if(!guia?.transportadora){
                return `
                    <div class="AVWG_Component_Guia">
                        <p class="AVWG_Component_Guia_values">
                            <?=$settings["guia_numeroguia"]?>: 
                            <strong>${guia?.numeroguia}</strong>
                        </p>
                        <p class="AVWG_Component_Guia_status">
                            Estado: Guía no Encontrada
                        </p>
                    </div>
                `
            }
            return `
                <div class="AVWG_Component_Guia">
                    <div class="AVWG_Component_Guia_top">
                        <div class="AVWG_Component_Guia_origen">
                           ${guia?.origen}
                           ${guia?.direccion}
                        </div>
                        <div class="AVWG_Component_Guia_destino">
                           ${guia?.destino_destinatario}
                           ${guia?.barrio_destinatario}
                           ${guia?.direccion_destinatario}
                        </div>
                    </div>
                    <p class="AVWG_Component_Guia_values">
                        <?=$settings["guia_numeroguia"]?>: 
                        <strong>${guia?.numeroguia}</strong>
                    </p>
                    <p class="AVWG_Component_Guia_values">
                        <?=$settings["guia_destinatario"]?>: 
                        <strong>${guia?.destinatario}</strong>
                    </p>
                    <p class="AVWG_Component_Guia_status">
                        <?=$settings["guia_nombreEstadoAve"]?>: 
                        <strong>${guia?.nombreEstadoAve}</strong>
                    </p>
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