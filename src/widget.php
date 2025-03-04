<?php
namespace Elementor;

if (!defined('ABSPATH')) exit; // Bloquear acceso directo

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
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        echo AVWG_Component_Widget($settings);
    }
}

