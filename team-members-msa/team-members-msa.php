<?php
// Prevent direct access to files
if ( ! defined( 'ABSPATH' ) ) {
	// exit;
    die('You are not allowed');
}

/*  
* Plugin Name:       Team Members MSA
* Plugin URI:        https://wordpress.org/plugins/team-members-msa/ 
* Description:       A custom plugin for managing team members. 
* Version:           1.0.0 
* Requires at least: 5.2 
* Requires PHP:      7.2 
* Author:            Md Shahidul Alam 
* Author URI:        https://dev-shahidulalam447.pantheonsite.io/ 
* License:           GPL v2 or later 
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html  
* Text Domain:       team-members-msa
* Domain Path:       /languages 
*/

// var_dump(__FILE__);
// exit();



class Team_Members_Plugin {
    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomy'));
        add_shortcode('team_members', array($this, 'team_members_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function register_post_type() {
        register_post_type('team_member', array(
            'labels' => array(
                'name' => __('Team Members'),
                'singular_name' => __('Team Member')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
        ));
    }

    public function register_taxonomy() {
        register_taxonomy('member_type', 'team_member', array(
            'hierarchical' => true,
            'labels' => array(
                'name' => __('Member Types'),
                'singular_name' => __('Member Type')
            ),
            'public' => true,
            'rewrite' => array('slug' => 'member-type'),
        ));
    }

    public function team_members_shortcode($atts) {
        $atts = shortcode_atts(array(
            'number' => -1,
            'image_position' => 'top',
            'show_button' => true,
        ), $atts);

        $query_args = array(
            'post_type' => 'team_member',
            'posts_per_page' => $atts['number'],
        );

        $team_members = new WP_Query($query_args);

        ob_start();
        if ($team_members->have_posts()) {
            while ($team_members->have_posts()) {
                $team_members->the_post();
                $position = get_field('position'); // Assuming 'position' is stored as a custom field
                $image = get_the_post_thumbnail_url();
                $name = get_the_title();
                $bio = get_the_content();
                $permalink = get_permalink();
?>
                <div class="team-member">
                    <?php if ($atts['image_position'] === 'bottom') : ?>
                        <div class="member-details">
                            <h3><a href="<?php echo $permalink; ?>"><?php echo $name; ?></a></h3>
                            <p><?php echo $position; ?></p>
                            <p><?php echo $bio; ?></p>
                        </div>
                        <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>">
                    <?php else : ?>
                        <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>">
                        <div class="member-details">
                            <h3><a href="<?php echo $permalink; ?>"><?php echo $name; ?></a></h3>
                            <p><?php echo $position; ?></p>
                            <p><?php echo $bio; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
            }
        }
        if ($atts['show_button']) {
            echo '<a href="' . get_post_type_archive_link('team_member') . '" class="see-all-button">See All</a>';
        }
        wp_reset_postdata();
        return ob_get_clean();
    }

    public function enqueue_styles() {
        wp_enqueue_style('team-members-style', plugins_url('style.css', __FILE__));
    }
}

new Team_Members_Plugin();
