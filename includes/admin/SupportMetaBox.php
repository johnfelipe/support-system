<?php

namespace SmartcatSupport\admin;

use SmartcatSupport\util\TemplateRender;
use SmartcatSupport\descriptor\Option;
use SmartcatSupport\form\FormBuilder;
use SmartcatSupport\form\field\TextBox;
use SmartcatSupport\form\field\SelectBox;
use SmartcatSupport\form\constraint\Choice;
use SmartcatSupport\form\constraint\Date;
use const SmartcatSupport\TEXT_DOMAIN;

/**
 * Metabox for support ticket information.
 * 
 * @since 1.0.0
 * @package admin
 * @author Eric Green <eric@smartcat.ca>
 */
class SupportMetaBox extends MetaBox {

    private $builder;
    private $view;

    public function __construct( TemplateRender $view, FormBuilder $builder ) {
        parent::__construct( 'ticket_meta', __( 'Ticket Information', TEXT_DOMAIN ), 'support_ticket' ); 

        $this->builder = $builder;
        $this->view = $view;
    }
    
    /**
     * @see \SmartcatSupport\admin\MetaBox
     * @param WP_Post $post The current post.
     * @since 1.0.0
     * @author Eric Green <eric@smartcat.ca>
     */
    public function render( $post ) {
        $form = $this->configure_form( $post );

        echo $this->view->render( 'metabox', [ 'form' => $form ] );
    }

    private function configure_form( $post ) {
        $agents = [ '' => __( 'No Agent Assigned', TEXT_DOMAIN ) ] + support_system_agents();
        $statuses = get_option( Option::STATUSES, Option\Defaults::STATUSES );

        //<editor-fold desc="Form Configuration">
        $this->builder->add( TextBox::class, 'email',
            [
                'type'              => 'email',
                'label'             => 'Contact Email',
                'value'             => get_post_meta( $post->ID, 'email', true ),
                'sanitize_callback' => 'sanitize_email'
            ]
        )->add( SelectBox::class, 'agent',
            [
                'label'       => 'Assigned To',
                'options'     => $agents,
                'value'       => get_post_meta( $post->ID, 'agent', true ),
                'constraints' => [
                    $this->builder->create_constraint( Choice::class, array_keys( $agents ) )
                ]
            ]
        )->add( SelectBox::class, 'status',
            [
                'label'       => 'Status',
                'options'     => $statuses,
                'value'       => get_post_meta( $post->ID, 'status', true ),
                'constraints' => [
                    $this->builder->create_constraint( Choice::class, array_keys( $statuses ) )
                ]
            ]
        );
        //</editor-fold>

        return apply_filters( 'support_ticket_metabox_form', $this->builder, $post )->get_form();
    }

    /**
     * @see \SmartcatSupport\admin\MetaBox
     * @param int $post_id The ID of the current post.
     * @param WP_Post $post The current post.
     * @since 1.0.0
     * @author Eric Green <eric@smartcat.ca>
     */
    public function save( $post_id, $post ) {
        $form = $this->configure_form( $post );

        if( $form->is_valid() ) {
            $data = $form->get_data();

            foreach( $data as $key => $value ) {
                update_post_meta( $post->ID, $key, $value );
            }
        }
    }
}
