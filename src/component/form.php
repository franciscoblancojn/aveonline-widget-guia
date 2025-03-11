<?php

function AVWG_Component_Form($settings)  {
    ob_start();
    $defaultGuias = '';
    if($settings['use_get'] == "yes"){
        $defaultGuias = $_GET['guias'] ?? '';
    }
    ?>
    <div class="AVWG_Component_Form">
        <h4 class="AVWG_Component_Form_title">
            <?=($settings["title"] ?? "Rastrea tu guía")?>
        </h4>
        <div class="AVWG_Component_Form_alert">
            <?=($settings["alert"] ?? "Hola, recuerda que puedes rastrear múltiples guías, separándolas por comas.")?>
        </div>
        <div class="AVWG_Component_Form_text">
            <?=($settings["text"] ?? "En caso de salir alguna NOVEDAD, debes comunicarte directamente con la tienda en donde hiciste la compra, pues son ellos quienes deben resolverla, para que tu pedido llegue pronto.")?>
        </div>
        <label>
            <div class="AVWG_Component_Form_label">
                <?=($settings["label"] ?? "Número de guía")?>
            </div>
            <input
                id="AVWG_Component_Form_input"
                type="text"
                placeholder="<?=($settings["placeholder"] ?? "Número de guía")?>"
                class="AVWG_Component_Form_input"
                value="<?=$defaultGuias?>"
            />
        </label>
        <div class="AVWG_Component_Form_content_btn">
            <button id="AVWG_Component_Form_btn" class="AVWG_Component_Form_btn" onclick="AVWG_onGetGuias()">
                <?=($settings["btn"] ?? "Buscar")?>
            </button>
        </div>
    </div>
    <style>
        .AVWG_Component_Form{
            display:grid;
            gap:.5rem;
        }
        .AVWG_Component_Form_title{
            width:100%;
        }
        .AVWG_Component_Form_text{
            width:100%;
        }
        .AVWG_Component_Form_input{
            outline: none;
        }
        .AVWG_Component_Form_content_btn{    
            display: flex;
        }
        .AVWG_Component_Form_btn{
            margin-left:auto;
            position: relative;
        }
        .AVWG_Component_Form_btn.loader{
            color:transparent !important;
        }
        .AVWG_Component_Form_btn.loader:before{
            content:"";
            width: 1.5rem;
            aspect-ratio: 1/1;
            border: 0.3rem solid var(--color);
            border-top-color: transparent;
            border-radius: 100%;
            margin: auto;
            animation: AVWG-to-rotate 1s infinite;
            display:block;
            position: absolute;
            inset:0;
        }
        @keyframes AVWG-to-rotate {
            to {
                transform: rotate(360deg);
            }
        }

    </style>
    <script>
        const AVWG_onGetGuias_Request = async(n) => {
            const numeroguia = `${n}`.replaceAll(" ","")
            try {
                const myHeaders = new Headers();
                myHeaders.append("Content-Type", "application/json");

                const raw = JSON.stringify({
                    "tipo": "infoGuiaP2PV3",
                    "guia": numeroguia
                });

                const requestOptions = {
                    method: "POST",
                    headers: myHeaders,
                    body: raw,
                    redirect: "follow"
                };

                const response = await fetch("https://app.aveonline.co/api/comunes/v2.0/guiasNacionalP2P.php", requestOptions)
                const result = await response.json()
                return {
                    ...result?.data?.[0],
                    numeroguia
                }
            } catch (e){
                return {
                    numeroguia
                };
            }
        }
        const AVWG_onGetGuias = async ()=>{
            const guias = `${document.getElementById("AVWG_Component_Form_input")?.value ?? ''}`.split(',')
            if(guias && guias.length > 0 && guias[0]!=''){
                const btn = document.getElementById("AVWG_Component_Form_btn")
                btn.classList.add("loader")
                const guiasResult = await Promise.all(guias.map(guia => AVWG_onGetGuias_Request(guia)));
                btn.classList.remove("loader")
                if(typeof AVWG_onGetGuias_callback == 'function' ){
                    AVWG_onGetGuias_callback(guiasResult)
                }
            }
        }
        AVWG_onGetGuias()
    </script>
    <?php
    return ob_get_clean();
}