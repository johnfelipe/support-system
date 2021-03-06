<?php

use smartcat\form\ChoiceConstraint;
use smartcat\form\Form;
use smartcat\form\SelectBoxField;
use smartcat\form\TextBoxField;

$products = \SmartcatSupport\util\products();
$products = array( 0 => __( 'Select a Product', \SmartcatSupport\PLUGIN_ID ) ) + $products;

$form = new Form( 'product_metabox' );

$form->add_field( new TextBoxField(
    array(
        'name'              => 'receipt_id',
        'class'             => array( 'metabox-field' ),
        'type'              => 'text',
        'label'             => __( 'Receipt #', \SmartcatSupport\PLUGIN_ID ),
        'value'             => get_post_meta( $post->ID, 'receipt_id', true ),
        'sanitize_callback' => 'sanitize_text_field'
    )

) )->add_field( new SelectBoxField(
    array(
        'id'          => 'product',
        'class'       => array( 'metabox-field' ),
        'label'       => __( 'Product', \SmartcatSupport\PLUGIN_ID ),
        'value'       => get_post_meta( $post->ID, 'product', true ),
        'options'     => $products,
        'constraints' => array(
            new ChoiceConstraint( array_keys( $products ) )
        )
    )

) );

return $form;
