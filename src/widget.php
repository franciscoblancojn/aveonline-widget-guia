<?php
namespace Elementor;

if (!defined('ABSPATH')) exit; // Bloquear acceso directo

use Elementor\Group_Control_Dimensions;
use Elementor\Group_Control_Background; 
use Elementor\Group_Control_Border;

class AVWG_AveFormGuias extends Widget_Base {

    public function get_name() {
        return 'ave_form_guias';
    }

    public function get_title() {
        return __('Aveonline Formulario Guias', 'plugin-name');
    }

    public function get_icon() {
        return 'eicon-star';
    }

    public function get_categories() {
        return ['general'];
    }
    private function addStyleControler($key,$name,$class) {

        $this->start_controls_section(
            $key.'_style',
            [
                'label' => __($name, 'plugin-name'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        // Control de color del texto
        $this->add_control(
            $key.'_color',
            [
                'label' => __('Color', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .'.$class => 'color: {{VALUE}};--color: {{VALUE}};',
                ],
            ]
        );

        // Control de tipografía
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => $key.'_typography',
                'selector' => '{{WRAPPER}} .'.$class,
            ]
        );

        // Control de espaciado (margen y padding)
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => $key.'_box_shadow',
                'selector' => '{{WRAPPER}} .'.$class,
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => $key.'_background',
                'label' => __('Fondo', 'plugin-name'),
                'types' => ['classic', 'gradient', 'video'],
                'selector' => '{{WRAPPER}} .'.$class,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => $key.'_border',
                'label' => __('Borde', 'plugin-name'),
                'selector' => '{{WRAPPER}} .'.$class,
            ]
        );
        $this->add_control(
            $key.'_border_radius',
            [
                'label' => __('Radio de borde', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .'.$class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            $key.'_padding',
            [
                'label' => __('Padding', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .'.$class  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            $key.'_margin',
            [
                'label' => __('Margin', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .'.$class  => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        

        $this->end_controls_section(); // Cerrar la sección de estilos

    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Contenido', 'plugin-name'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Titulo', 'plugin-name'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Rastrea tu guía', 'plugin-name'),
            ]
        );
        $this->add_control(
            'text',
            [
                'label' => __('Texto', 'plugin-name'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => __('En caso de salir alguna NOVEDAD, debes comunicarte directamente con la tienda en donde hiciste la compra, pues son ellos quienes deben resolverla, para que tu pedido llegue pronto.', 'plugin-name'),
            ]
        );
        $this->add_control(
            'alert',
            [
                'label' => __('Alerta', 'plugin-name'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => __('Hola, recuerda que puedes rastrear múltiples guías, separándolas por comas.', 'plugin-name'),
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Formulario', 'plugin-name'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'label',
            [
                'label' => __('Label', 'plugin-name'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Número de guía', 'plugin-name'),
            ]
        );
        $this->add_control(
            'placeholder',
            [
                'label' => __('Placeholder', 'plugin-name'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Número de guía', 'plugin-name'),
            ]
        );
        $this->add_control(
            'btn',
            [
                'label' => __('Boton', 'plugin-name'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Buscar', 'plugin-name'),
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Guía', 'plugin-name'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'guia_origen',
            [
                'label' => __('Origen', 'plugin-name'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Origen', 'plugin-name'),
            ]
        );
        $this->add_control(
            'guia_destino',
            [
                'label' => __('Destino', 'plugin-name'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Destino', 'plugin-name'),
            ]
        );
        $this->add_control(
            'guia_numeroguia',
            [
                'label' => __('Número de guía', 'plugin-name'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Número de guía', 'plugin-name'),
            ]
        );
        $this->add_control(
            'guia_destinatario',
            [
                'label' => __('Nombre', 'plugin-name'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Nombre', 'plugin-name'),
            ]
        );
        $this->add_control(
            'guia_nombreEstadoAve',
            [
                'label' => __('Estado', 'plugin-name'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Estado', 'plugin-name'),
            ]
        );
        $this->end_controls_section();


        $this->addStyleControler('Titulo','Titulo','AVWG_Component_Form_title');
        $this->addStyleControler('Alerta','Alerta','AVWG_Component_Form_alert');
        $this->addStyleControler('Texto','Texto','AVWG_Component_Form_text');
        $this->addStyleControler('Label','Label','AVWG_Component_Form_label');
        $this->addStyleControler('Input','Input','AVWG_Component_Form_input');
        $this->addStyleControler('Boton','Boton','AVWG_Component_Form_btn');
        $this->addStyleControler('guia','Guía','AVWG_Component_Guia');
        $this->addStyleControler('origen','Origen','AVWG_Component_Guia_origen');
        $this->addStyleControler('destino','Destino','AVWG_Component_Guia_destino');
        $this->addStyleControler('numeroguia','Número de guía','AVWG_Component_Guia_numeroguia');
        $this->addStyleControler('destinatario','Nombre','AVWG_Component_Guia_destinatario');
        $this->addStyleControler('destino','Destino','AVWG_Component_Guia_destinatario');
        $this->addStyleControler('nombreEstadoAve','Estado','AVWG_Component_Guia_status');


    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        echo AVWG_Component_Widget($settings);
    }
}

