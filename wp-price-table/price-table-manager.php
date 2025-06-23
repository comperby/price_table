<?php
/*
Plugin Name: Price Table Manager
Description: Manage services and prices with Elementor integration.
Version: 0.1.0
Author: CodexBot
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class PT_Manager {

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widget' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        if ( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post_pt_service', array( $this, 'save_price_meta' ) );
        }
    }

    public function register_post_type() {
        $labels = array(
            'name' => 'Services',
            'singular_name' => 'Service'
        );
        $args = array(
            'label' => 'Services',
            'public' => false,
            'show_ui' => true,
            'supports' => array( 'title', 'editor' )
        );
        register_post_type( 'pt_service', $args );
    }

    public function register_taxonomies() {
        register_taxonomy( 'pt_category', 'pt_service', array(
            'label' => 'Categories',
            'hierarchical' => true,
            'show_ui' => true
        ) );

        register_taxonomy( 'pt_price_group', 'pt_service', array(
            'label' => 'Price Groups',
            'hierarchical' => false,
            'show_ui' => true
        ) );
    }

    public function admin_menu() {
        add_menu_page( 'Price Table', 'Price Table', 'manage_options', 'pt-manager', array( $this, 'render_admin_page' ), 'dashicons-list-view' );
    }

    public function render_admin_page() {
        echo '<div class="wrap"><h1>Price Table Manager</h1>';
        echo '<p>Manage services, categories and price groups.</p>';
        echo '</div>';
    }

    public function add_meta_boxes() {
        add_meta_box( 'pt_price_meta', __( 'Service Price', 'pt' ), array( $this, 'price_meta_box' ), 'pt_service', 'side' );
    }

    public function price_meta_box( $post ) {
        wp_nonce_field( 'pt_price_meta', 'pt_price_nonce' );
        $value = get_post_meta( $post->ID, '_pt_price', true );
        echo '<label for="pt_price">' . __( 'Price', 'pt' ) . '</label>';
        echo '<input type="text" id="pt_price" name="pt_price" value="' . esc_attr( $value ) . '" />';
    }

    public function save_price_meta( $post_id ) {
        if ( ! isset( $_POST['pt_price_nonce'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( $_POST['pt_price_nonce'], 'pt_price_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( isset( $_POST['pt_price'] ) ) {
            update_post_meta( $post_id, '_pt_price', sanitize_text_field( $_POST['pt_price'] ) );
        }
    }

    public function enqueue_assets() {
        wp_enqueue_style( 'pt-style', plugin_dir_url( __FILE__ ) . 'assets/style.css' );
        wp_enqueue_script( 'pt-script', plugin_dir_url( __FILE__ ) . 'assets/script.js', array(), null, true );
    }

    public function register_widget() {
        if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
            return;
        }
        require_once __DIR__ . '/widget/class-pt-widget.php';
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PT_Widget() );
    }
}

new PT_Manager();

