<?php

namespace SmartcatSupport\util;

use function SmartcatSupport\agents_dropdown;
use SmartcatSupport\descriptor\Option;
use const SmartcatSupport\TEXT_DOMAIN;
use const SmartcatSupport\PLUGIN_VERSION;

/**
 *  Installs plugin components 
 * 
 *  @author Eric Green <eric@smartcat.ca>
 *  @since 1.0.0
 */
final class Installer extends ActionListener {

    public function __construct() {
        $this->add_action( 'init', 'register_post_type' );
    }

    public function activate() {
        update_option( Option::PLUGIN_VERSION, PLUGIN_VERSION );

        $this->register_template();
        $this->add_user_roles();
    }
    
    public function deactivate() {
        unregister_post_type( 'support_ticket' );

        $this->remove_user_roles();
    }

    public function add_user_roles() {
        add_role( 'support_admin', __( 'Support Admin', TEXT_DOMAIN ), array(
            'view_support_tickets'      => true,
            'create_support_tickets'    => true,
            'unfiltered_html'           => true,
            'edit_others_tickets'       => true
        ) );

        add_role( 'support_agent', __( 'Support Agent', TEXT_DOMAIN ), array(
            'view_support_tickets'      => true,
            'unfiltered_html'           => true,
            'edit_others_tickets'       => true
        ) );

        add_role( 'support_user', __( 'Support User', TEXT_DOMAIN ), array(
            'view_support_tickets'      => true,
            'create_support_tickets'    => true,
            'unfiltered_html'           => true
        ) );

        $role = get_role( 'administrator' );
        $role->add_cap( 'view_support_tickets' );
        $role->add_cap( 'unfiltered_html' );
        $role->add_cap( 'edit_others_tickets' );
        $role->add_cap( 'create_support_tickets' );
    }
    
    public function remove_user_roles() {
        $role = get_role( 'administrator' );
        $role->remove_cap( 'view_support_tickets' );
        $role->remove_cap( 'unfiltered_html' );
        $role->remove_cap( 'edit_others_tickets' );
        $role->remove_cap( 'create_support_tickets' );

        remove_role( 'support_admin' );
        remove_role( 'support_agent' );
        remove_role( 'support_user' );
    }

    public function register_template() {
        $post_id = null;
        $post = get_post( get_option( Option::TEMPLATE_PAGE_ID ) ) ;

        if( empty( $post ) ) {
            $post_id = wp_insert_post(
                array(
                    'post_type' =>  'page',
                    'post_status' => 'publish',
                    'post_title' => __( 'Support', TEXT_DOMAIN )
                )
            );
        } else if( $post->post_status == 'trash' ) {
            wp_untrash_post( $post->ID );

            $post_id = $post->ID;
        } else {
            $post_id = $post->ID;
        }

        if( !empty( $post_id ) ) {
            update_option( Option::TEMPLATE_PAGE_ID, $post_id );
        }
    }

    public function register_post_type() {
        $labels = array(
            'name'                  => _x( 'Support Tickets', 'Post Type General Name', TEXT_DOMAIN ),
            'singular_name'         => _x( 'Support Ticket', 'Post Type Singular Name', TEXT_DOMAIN ),
            'menu_name'             => __( 'Support Tickets', TEXT_DOMAIN ),
            'name_admin_bar'        => __( 'Support Tickets', TEXT_DOMAIN ),
            'archives'              => __( 'Item Archives', TEXT_DOMAIN ),
            'parent_item_colon'     => __( 'Parent Item:', TEXT_DOMAIN ),
            'all_items'             => __( 'All Tickets', TEXT_DOMAIN ),
            'add_new_item'          => __( 'New Ticket', TEXT_DOMAIN ),
            'add_new'               => __( 'New Ticket', TEXT_DOMAIN ),
            'new_item'              => __( 'New Ticket', TEXT_DOMAIN ),
            'edit_item'             => __( 'Edit Ticket', TEXT_DOMAIN ),
            'update_item'           => __( 'Update Ticket', TEXT_DOMAIN ),
            'view_item'             => __( 'View Ticket', TEXT_DOMAIN ),
            'search_items'          => __( 'Search Ticket', TEXT_DOMAIN ),
            'not_found'             => __( 'Ticket Not found', TEXT_DOMAIN ),
            'not_found_in_trash'    => __( 'Ticket Not found in Trash', TEXT_DOMAIN ),
            'featured_image'        => __( 'Featured Image', TEXT_DOMAIN ),
            'set_featured_image'    => __( 'Set featured image', TEXT_DOMAIN ),
            'remove_featured_image' => __( 'Remove featured image', TEXT_DOMAIN ),
            'use_featured_image'    => __( 'Use as featured image', TEXT_DOMAIN ),
            'insert_into_item'      => __( 'Insert into ticket', TEXT_DOMAIN ),
            'uploaded_to_this_item' => __( 'Uploaded to this ticket', TEXT_DOMAIN ),
            'items_list'            => __( 'Tickets list', TEXT_DOMAIN ),
            'items_list_navigation' => __( 'Tickets list navigation', TEXT_DOMAIN ),
            'filter_items_list'     => __( 'Filter tickets list', TEXT_DOMAIN )
        );

        $capabilities = array();

        $args = array(
            'label'               => __( 'Support Ticket', TEXT_DOMAIN ),
            'description'         => __( 'Tickets for support requests', TEXT_DOMAIN ),
            'labels'              => $labels,
            'supports'            => array( 'editor', 'author', 'comments', 'title' ),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 70,
            'menu_icon'           => 'dashicons-sos',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capabilities'        => $capabilities
        );

        register_post_type( 'support_ticket', $args );
    }
}
