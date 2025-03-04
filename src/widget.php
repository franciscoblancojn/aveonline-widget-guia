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
            'text',
            [
                'label' => __('Texto', 'plugin-name'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Hola, Elementor!', 'plugin-name'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        echo '<h2>' . esc_html($settings['text']) . '</h2>';
        echo AVWG_Component_Widget();
    }
}

