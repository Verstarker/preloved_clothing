<?php
/*
Plugin Name: Testy  Testimonials
Description: Wordpress plugin for testimonials
Version: 0.1
Author: Hayden Wrathall
*/
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Custom Post Types
 */

function testy_register_post_type(){
    register_post_type('testy_testimonial',
        array(
            'labels' => array (
                'name' => __('Testimonials'),
                'singular_name' => __('Testimonial')
            ),
            'public' => true,
            'has_archive' => true,
            'menu_position' => 5,
            'supports' => array (
                'title',
                'editor',
                'thumbnail'
            )
        ));
}

add_action('init', 'testy_register_post_type');

/**
 * Custom Meta Boxes
 */


function testy_org_name_meta_box(){
    global $post;
    wp_nonce_field(basename(__FILE__, 'testy-org-name-nonce'));
    ?>
    <label for="testy-org-name">
        <?php _e("Write the name of the person's organization") ?>
    </label>
    <input type="text" name="testy-org-name" value="<?php esc_attr(get_post_meta( $post->ID, 'testy-org-name')) ?>">
    <?php
}


function testy_add_post_meta_boxes(){
    add_meta_box('testy-org-name-mb',
        'testy_org_name_meta_box', //Callback
        'testy-testimonial',
        'normal',
        'default'
    );
}


function testy_save_org_name($post_id, $post){
    if(isset($post['testy-org-name-nonce']) ||
        !wp_verify_nonce($_POST['testy-org-name-nonce'], (__FILE__))
    ) {
        return $post_id;
    }

    $post_type = get_post_type_object($post->post_type);

    if(!current_user_can($post_type->cap->edit_post, $post_id)){
        return $post_id;
    }

    $new_meta_value = (isset($_POST['testy-org-name']) ? $_POST['testy-org-name'] : '');
    $meta_key = 'testy_org_name';
    $meta_value = get_post_meta($post_id, $meta_key, true);
    if($new_meta_value && $meta_value == '') {
        add_post_meta($post_id, $meta_key, $new_meta_value != $meta_value);
    } else if($new_meta_value && $new_meta_value != $meta_value){
        update_post_meta($post_id, $meta_key, $new_meta_value);
    }else if($new_meta_value == ''  && $meta_value){
        delete_post_meta($post_id, $meta_key, $new_meta_value);
    }

}

function testy_post_meta_boxes_setup(){
    add_action('add_meta_boxes', '');
    add_action('save_post', '');
}

add_action('load-post.php', 'testy_post_meta_boxes_setup');
add_action('load-post-new.php', 'testy_post_meta_boxes_setup');