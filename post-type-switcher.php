<?php
/*
Plugin Name: Post Type Switcher
Plugin URI: http://wordpress.org
Description: Allow switching of post_type in post publish area
Author: John James Jacoby
Version: 0.1
Author URI: http://johnjamesjacoby.com
*/

/**
 * pts_metabox()
 *
 * Adds post_publish metabox to allow changing post_type
 *
 * @global object $post Current post
 */
function pts_metabox() {
	global $post;

	$post_types =				get_post_types();
	$cur_post_type =			$post->post_type;
	$cur_post_type_object =		get_post_type_object( $cur_post_type );
	$can_publish =				current_user_can( $cur_post_type_object->cap->publish_posts );
?>

<div class="misc-pub-section misc-pub-section-last post-type-switcher">
	<label for="pts_post_type"><?php _e( 'Post Type:' ); ?></label>
	<span id="post-type-display"><?php echo $cur_post_type_object->label; ?></span>
<?php	if ( $can_publish ) : ?>
	<a href="#pts_post_type" class="edit-post-type hide-if-no-js"><?php _e( 'Edit' ); ?></a>
	<div id="post-type-select" class="hide-if-js">
		<select name="pts_post_type" id="pts_post_type">
<?php
		foreach ( $post_types as $post_type ) {
			$pt = get_post_type_object( $post_type );
			if ( current_user_can( $pt->cap->publish_posts ) ) :
?>
			<option value="<?php echo $pt->name; ?>"<?php if ( $cur_post_type == $post_type ) : ?>selected="selected"<?php endif; ?>><?php echo $pt->label; ?></option>
<?php
			endif;
		}
?>
		</select>
		<input type="hidden" name="hidden_post_type" id="hidden_post_type" value="<?php echo $cur_post_type; ?>" />
		<a href="#pts_post_type" class="save-post-type hide-if-no-js button"><?php _e( 'OK' ); ?></a>
		<a href="#pts_post_type" class="cancel-post-type hide-if-no-js"><?php _e( 'Cancel' ); ?></a>
	</div>
</div>
<?php
	endif;
}
add_action( 'post_submitbox_misc_actions', 'pts_metabox' );

/**
 * pts_head()
 *
 * Adds needed JS and CSS to admin header
 */
function pts_head() {
?>
	<script type='text/javascript'>
		jQuery(document).ready(function(){
			jQuery('#post-type-select').siblings('a.edit-post-type').click(function() {
				if (jQuery('#post-type-select').is(":hidden")) {
					jQuery('#post-type-select').slideDown("normal");
					jQuery(this).hide();
				}
				return false;
			});

			jQuery('.save-post-type', '#post-type-select').click(function() {
				jQuery('#post-type-select').slideUp("normal");
				jQuery('#post-type-select').siblings('a.edit-post-type').show();
				pts_updateText();
				return false;
			});

			jQuery('.cancel-post-type', '#post-type-select').click(function() {
				jQuery('#post-type-select').slideUp("normal");
				jQuery('#pts_post_type').val(jQuery('#hidden_post_type').val());
				jQuery('#post-type-select').siblings('a.edit-post-type').show();
				pts_updateText();
				return false;
			});

			function pts_updateText() {
				jQuery('#post-type-display').html( jQuery('#pts_post_type :selected').text() );
				jQuery('#hidden_post_type').val(jQuery('#pts_post_type').val());
				jQuery('#post_type').val(jQuery('#pts_post_type').val());
				return true;
			}
		});
	</script>
	<style type="text/css">
		#post-type-select {
			line-height: 2.5em;
			margin-top: 3px;
		}
		#post-type-display {
			font-weight: bold;
		}
		div.post-type-switcher {
			border-top: 1px solid #eee;
		}
	</style>
<?php
}
add_action( 'admin_head', 'pts_head' );

?>
