<?php

use SmartcatSupport\descriptor\Option;

$attachments = get_attached_media( 'image', $ticket->ID );
$attachment_count = count( $attachments );

$user = wp_get_current_user();

?>
<div class="loader-mask"></div>

<div class="row ticket-detail" style="display: none">

    <div class="sidebar col-sm-4 col-sm-push-8"><p class="text-center"><?php _e( 'Loading...', \SmartcatSupport\PLUGIN_ID ); ?></p></div>

    <div class="discussion-area col-sm-8 col-sm-pull-4">

        <div class="ticket panel panel-default ">

            <div class="panel-heading">

                <p class="panel-title"><?php esc_html_e( $ticket->post_title ); ?></p>

            </div>

            <div class="panel-body">

                <p><?php echo $ticket->post_content; ?></p>

            </div>

        </div>

        <div class="comments"></div>

        <div class="comment-reply-wrapper">

            <div class="comment comment-reply panel panel-default">

                <div class="panel-heading">

                    <div class="media pull-left meta">

                        <div class="media-left">

                            <?php echo get_avatar( $user->ID, 28, '', '', array( 'class' => 'img-circle media-object' ) ); ?>

                        </div>

                        <div class="media-body" style="width: auto">

                            <p class="media-heading"><?php echo $user->first_name . ' ' . $user->last_name; ?></p>

                        </div>

                    </div>

                    <div class="clearfix"></div>

                </div>

                <div class="panel-body">

                    <div class="editor">

                        <form class="comment-form">

                            <textarea class="editor-content" name="content" rows="5"></textarea>

                            <input type="hidden" name="id" value="<?php echo $ticket->ID; ?>">

                            <div class="bottom">

                                <span class="text-right">

                                    <button type="submit" class="button button-submit" disabled="true">

                                        <span class="glyphicon glyphicon-send button-icon"></span>

                                        <span><?php _e( get_option( Option::REPLY_BTN_TEXT, Option\Defaults::REPLY_BTN_TEXT ) ); ?></span>

                                    </button>

                                </span>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<div id="attachment-modal-<?php echo $ticket->ID; ?>"
     data-ticket_id="<?php echo $ticket->ID; ?>"
     class="modal attachment-modal fade">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title"><?php _e( 'Attach Images', \SmartcatSupport\PLUGIN_ID ); ?></h4>

            </div>

            <div class="modal-body">

                <form id="attachment-dropzone-<?php echo $ticket->ID; ?>" class="dropzone">

                    <?php wp_nonce_field( 'support_ajax', '_ajax_nonce' ); ?>

                    <input type="hidden" name="ticket_id" value="<?php echo $ticket->ID; ?>" />

                </form>

            </div>

            <div class="modal-footer">

                <button type="button" class="button button-submit close-modal"
                        data-target="#attachment-modal-<?php echo $ticket->ID; ?>"
                        data-toggle="modal">

                    <?php _e( 'Done', \SmartcatSupport\PLUGIN_ID ); ?>

                </button>

            </div>

        </div>

    </div>

</div>
