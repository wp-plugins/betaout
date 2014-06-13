<?php

class WpPull {

    // move post
    public static function moveBoPost($wpPost) {
        $personaUserId = $wpPost['post_author'];
        $email = $wpPost['post_email'];
       $userName=$wpPost['post_user_name'];
        $wpUserId = 0;
        $wpUserId = self::getUserIdByEmail($email,$userName);

        if ($wpUserId == 0) {
            $wpUserId = 1;
        }

        $wpPost['post_author'] = $wpUserId;

        $storyfolderId = $wpPost['storyfolderId'];
        $featureImage = $wpPost['featureImage'];
        $betaoutSeoTitle = $wpPost['seo_title'];
        $betaoutMetaDesc = $wpPost['meta_description'];
        unset($wpPost['storyfolderId'], $wpPost['featureImage'], $wpPost['seo_title'], $wpPost['meta_description']);

        kses_remove_filters();

        if (NULL == get_post($wpPost['ID'])) {
            $wpPost['ID'] = 0;
        }

        $wpPost['post_date'] = gmdate('Y-m-d H:i:s', ( strtotime($wpPost['post_date_gmt']) + ( get_option('gmt_offset') * HOUR_IN_SECONDS )));

        $post_id = wp_insert_post($wpPost, $wp_error);

        $data = array();

        if ($post_id) {
            update_post_meta($post_id, 'storyfolderId', $storyfolderId);
            update_post_meta($post_id, 'betaout_seotitle', $betaoutSeoTitle);
            update_post_meta($post_id, 'betaout_metadesc', $betaoutMetaDesc);
            $current_thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
            $current_cc_thumbnail_id = get_post_meta($current_thumbnail_id, '_cc_thumbnail_id', true);

            if ($current_cc_thumbnail_id != $featureImage['guid']) {
                if (count($featureImage) > 0) {
                    try {
                        $url =$featureImage['guid'];
                        $result = media_sideload_image($url, $post_id);
                        $attachments = get_posts(array('numberposts' => '1', 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image','orderby' => 'post_date','order' => 'DESC'));
                        if (sizeof($attachments) > 0) {
                            set_post_thumbnail($post_id, $attachments[0]->ID);
                            update_post_meta($attachments[0]->ID, '_cc_thumbnail_id', $featureImage['guid']);
                        }
                    } catch (Exception $e) {
                       $error=$e->getMessage();
                    }
                }
            }

            clean_post_cache($post_id);
            $getPost = get_post($post_id);
            $categories = wp_get_post_categories($post_id);

            $data = array(
                'wpId' => $post_id,
                'storySlug' => $getPost->post_name,
                'categories' => $categories,
                'storyPermalink' => get_permalink($post_id),
                'wpversion' => $version,
                'wpUserId' => $wpUserId,
                'error'=> $error
            );
        }
        return $data;
    }

    // delete wordpress post
    public static function deleteBoPost($wpId) {
        wp_trash_post($wpId);
    }

    public function getUserIdByEmail($email,$authorName) {
        $userEmail = $email;
        $wp_user_obj = get_user_by('email', $userEmail);
        $wp_user_id = $wp_user_obj->ID;
        if (empty($wp_user_id)) {
            $user_pass = wp_generate_password();
            $user_name = explode('@', $email);
            $user_name = str_replace("_", " ", $user_name[0]);
            $fname =$authorName;
            $role = get_option('default_role');
            $nameexists = true;
            $index = 0;
            $username = str_replace(' ', '-', $user_name);

            $userName = $username;

            while ($nameexists == true) {
                if (username_exists($userName) != 0) {
                    $index++;
                    $userName = $username . $index;
                } else {
                    $nameexists = false;
                }
            }

            $userdata = array(
                'user_login' => $userName,
                'user_pass' => $user_pass,
                'user_nicename' => sanitize_title($fname),
                'user_email' => $userEmail,
                'display_name' => $fname,
                'nickname' => $fname,
                'first_name' => $fname,
                'role' => $role
            );
            $wp_user_id = wp_insert_user($userdata);
        }

        if (is_multisite ()) {
            global $blog_id; // required
            $my_current_blog_id = get_current_blog_id();
            if (is_user_member_of_blog($user_id, $my_current_blog_id)) {
                
            } else {
                add_user_to_blog($my_current_blog_id, $wp_user_id, 'subscriber');
            }
        }
        return $wp_user_id;
    }

}
