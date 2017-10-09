<?php

/**
 * Insert an attachment from an URL address.
 *
 * @param  String $url
 * @param  array  $meta_data
 * @param  Int    $post_id
 *
 * @return Int    Attachment ID
 */
function wp_insert_attachment_from_url($url, $meta_data = [], $post_id = null)
{
    if (!class_exists('WP_Http')) {
        include_once(ABSPATH.WPINC.'/class-http.php');
    }

    $http = new WP_Http();
    $response = $http->request($url);
    if ($response['response']['code'] != 200) {
        return false;
    }

    $upload = wp_upload_bits(basename($url), null, $response['body']);
    if (!empty($upload['error'])) {
        return false;
    }

    $file_path = $upload['file'];
    $file_name = basename($file_path);
    $file_type = wp_check_filetype($file_name, null);
    $attachment_title = sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME));
    $wp_upload_dir = wp_upload_dir();

    $post_info = array_merge(
        [
            'guid' => $wp_upload_dir['url'].'/'.$file_name,
            'post_mime_type' => $file_type['type'],
            'post_title' => $attachment_title,
            'post_content' => '',
            'post_status' => 'inherit',
        ],
        $meta_data
    );

    // Create the attachment
    $attach_id = wp_insert_attachment($post_info, $file_path, $post_id);

    // Include image.php
    require_once(ABSPATH.'wp-admin/includes/image.php');

    // Define attachment metadata
    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);

    // Assign metadata to attachment
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;

}

/**
 * Update a attachment from an URL address and update media
 *
 * @param  integer $media_id
 * @param  String  $url
 *
 * @return Int    Attachment ID
 */
function wp_update_attachment_from_url($media_id, $url)
{
    if (!class_exists('WP_Http')) {
        include_once(ABSPATH.WPINC.'/class-http.php');
    }

    $http = new WP_Http();
    $response = $http->request($url);
    if ($response['response']['code'] != 200) {
        return false;
    }

    $upload = wp_upload_bits(basename($url), null, $response['body']);
    if (!empty($upload['error'])) {
        return false;
    }

    $file_path = $upload['file'];
    $meta = wp_generate_attachment_metadata($media_id, $file_path);
    if (isset($meta['file'])) {
        wp_update_attachment_metadata($media_id, $meta);
        update_post_meta($media_id, '_wp_attached_file', $meta['file']);
    }
}
