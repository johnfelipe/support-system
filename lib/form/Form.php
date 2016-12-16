<?php

namespace smartcat\form;

if( !class_exists( '\smartcat\form\Form' ) ) :

class Form {
    public $id;
    public $method;
    public $action;
    public $valid = false;
    public $fields = array();
    public $data = array();
    public $errors = array();

    public function __construct( $id, $method = '', $action = '' ) {
        $this->id = $id;
        $this->method = strtoupper( $method );
        $this->action = $action;
    }

    public function add_field( Field $field ) {
        $this->fields[ $field->id ] = $field;

        return $this;
    }

    public function is_valid() {
        $valid = true;

        if ( $this->is_submitted() ) {
            foreach ( $this->fields as $id => $field ) {
                if ( $field->validate( $_REQUEST[ $id ] ) ) {
                    $this->data[ $id ] = $field->sanitize( $_REQUEST[ $id ] );
                } else {
                    $this->errors[ $id ] = $field->error_message;
                    $valid = false;
                }
            }

            $this->valid = $valid;
        }

        return $valid;
    }

    public function is_submitted() {
        return isset( $_REQUEST[ $this->id ] );
    }

    public static function render_fields( Form $form ) { ?>

            <?php foreach ( $form->fields as $field ) : ?>

            <p>

                <?php if( !empty( $field->label ) ) : ?>

                    <label><?php echo $field->label; ?></label>

                <?php endif; ?>

                <?php $field->render(); ?>

                <?php if( !empty( $field->desc ) ) : ?>

                    <p class="description"><?php echo $field->desc; ?></p>

                <?php endif; ?>

            </p>

        <?php endforeach; ?>

        <input type="hidden" name="<?php esc_attr_e( $form->id ); ?>" />

    <?php }

    public static function render( Form $form ) { ?>

        <form id="<?php esc_attr_e( $form->id ); ?>"
            method="<?php esc_attr_e( $form->method ); ?>"
            action="<?php esc_attr_e( $form->action ); ?>">

            <?php Form::render_fields( $form ); ?>

        </form>

    <?php }
}

endif;