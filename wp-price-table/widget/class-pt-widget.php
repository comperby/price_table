<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PT_Widget extends Widget_Base {

    public function get_name() {
        return 'pt_price_table';
    }

    public function get_title() {
        return __( 'Price Table', 'pt' );
    }

    public function get_icon() {
        return 'eicon-table';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'pt' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'category',
            [
                'label' => __( 'Category', 'pt' ),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_categories_options(),
            ]
        );

        $this->end_controls_section();
    }

    private function get_categories_options() {
        $terms = get_terms( [
            'taxonomy' => 'pt_category',
            'hide_empty' => false,
        ] );
        $options = [];
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $options[ $term->term_id ] = $term->name;
            }
        }
        return $options;
    }

    protected function render() {
        $settings = $this->get_settings();
        $category = isset( $settings['category'] ) ? intval( $settings['category'] ) : 0;
        $args = [
            'post_type' => 'pt_service',
            'posts_per_page' => -1,
        ];
        if ( $category ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'pt_category',
                    'field' => 'term_id',
                    'terms' => $category,
                ],
            ];
        }
        $query = new WP_Query( $args );
        echo '<table class="pt-table">';
        echo '<thead><tr><th>' . __( 'Service', 'pt' ) . '</th><th>' . __( 'Price', 'pt' ) . '</th></tr></thead>';
        echo '<tbody>';
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $price = get_post_meta( get_the_ID(), '_pt_price', true );
                echo '<tr>';
                echo '<td>' . esc_html( get_the_title() ) . '</td>';
                echo '<td>' . esc_html( $price ) . '</td>';
                echo '</tr>';
            }
        }
        wp_reset_postdata();
        echo '</tbody></table>';
    }
}
