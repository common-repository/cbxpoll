<?php

namespace CBXPollSingleElemWidget\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * CBX Poll Widget
 */
class CBXPollSingle_ElemWidget extends \Elementor\Widget_Base
{

    /**
     * Retrieve widget name.
     *
     * @return string Widget name.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_name()
    {
        return 'cbxpoll_single';
    }

    /**
     * Retrieve widget title.
     *
     * @return string Widget title.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_title()
    {
        return esc_html__('CBXPoll Single Widget', 'cbxpoll');
    }

    /**
     * Get widget categories.
     *
     * Retrieve the widget categories.
     *
     * @return array Widget categories.
     * @since  1.0.10
     * @access public
     *
     */
    public function get_categories()
    {
        return array('codeboxr');
    }

    /**
     * Retrieve widget icon.
     *
     * @return string Widget icon.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_icon()
    {
        return 'cbxpollsingle-icon';
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function _register_controls()
    {

        $this->start_controls_section(
            'section_cbxpollsingle',
            array(
                'label' => esc_html__('CBXPoll Single Widget Setting', 'cbxpoll'),
            )
        );

        /*if(!is_admin()){
            global $post;
        }

        $args = array(
            'post_type'      => 'cbxpoll',
            'orderby'        => 'ID',
            'order'          => 'DESC',
            'post_status'    => 'publish',
            'posts_per_page' => - 1,
        );

        $cbxpoll_posts = get_posts( $args );

        $cbxpoll_posts_arr = array();

        $cbxpoll_posts_arr[0] = esc_html__('Select Poll', 'cbxpoll');

        foreach ( $cbxpoll_posts as $post ) :
            \CBXPollHelper::setup_admin_postdata( $post );
            $post_id    = get_the_ID();
            $post_title = get_the_title();

            $cbxpoll_posts_arr[ $post_id ] = esc_attr( $post_title );
        endforeach;
        \CBXPollHelper::wp_reset_admin_postdata();*/


        $this->add_control(
            'cbxpoll_poll_id',
            array(
                'label'       => esc_html__('Poll Post ID', 'cbxpoll'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__('poll id here', 'cbxpoll'),
                'default'     => 0,
                //'options'     => $cbxpoll_posts_arr,
                'description' => esc_html__('Put poll post id', 'cbxpoll'),
                'label_block' => true
            )
        );

        $poll_display_methods = \CBXPollHelper::cbxpoll_display_options();
        $display_methods_arr  = array();

        foreach ($poll_display_methods as $key => $method) {
            $display_methods_arr[$key] = esc_attr($method['title']);
        }

        $display_methods_arr[''] = esc_html__('Use from poll post setting', 'cbxpoll');


        $this->add_control(
            'cbxpoll_chart_type',
            array(
                'label'       => esc_html__('Chart Type', 'cbxpoll'),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'placeholder' => esc_html__('Select Chart Type', 'cbxpoll'),
                'default'     => 'text',
                'options'     => $display_methods_arr,
                'label_block' => true
            )
        );

        $this->add_control(
            'cbxpoll_description',
            array(
                'label'       => esc_html__('Show Poll description', 'cbxpoll'),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'options'     => array(
                    1 => esc_html__('Yes', 'cbxpoll'),
                    0 => esc_html__('No', 'cbxpoll'),
                    2 => esc_html__('Use from poll post setting', 'cbxpoll'),
                ),
                'default'     => 1,
                'label_block' => true
            )
        );

        $this->add_control(
            'cbxpoll_grid',
            array(
                'label'       => esc_html__('Answer Format', 'cbxpoll'),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'options'     => array(
                    0 => esc_html__('List', 'cbxpoll'),
                    1 => esc_html__('Grid', 'cbxpoll'),
                    2 => esc_html__('Use Poll Post Setting', 'cbxpoll'),
                ),
                'default'     => 0,
                'label_block' => true
            )
        );

        $this->end_controls_section();
    }//end method _register_controls

    /**
     * Render google maps widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render()
    {
        $settings = $this->get_settings();

        $id          = intval($settings['cbxpoll_poll_id']);
        $chart_type  = sanitize_text_field($settings['cbxpoll_chart_type']);
        $description = intval($settings['cbxpoll_description']);
        $grid        = intval($settings['cbxpoll_grid']);


        if (intval($id) <= 0 && (false !== get_post_status($id))) {
            esc_html_e('Poll id missing or poll doesn\'t exists', 'cbxpoll');
        } else {

            if ($description == 2) {
                $description = '';
            } //2 = means ignore shortcode params, use from poll
            if ($grid == 2) {
                $grid = '';
            } //2 = means ignore shortcode params, use from poll

            echo do_shortcode('[cbxpoll id="'.$id.'" description="'.$description.'" chart_type="'.$chart_type.'" grid="'.$grid.'"]');
        }
    }//end method render

    /**
     * Render google maps widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function _content_template()
    {
    }//end method _content_template
}//end method CBXGooglemap_ElemWidget
