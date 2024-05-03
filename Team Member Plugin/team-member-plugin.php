<?php
/*
Plugin Name: Team Members Plugin
Description: Plugin to manage team members on your WordPress site.
Version: 1.0
Author: Orbit Technology || Md. Mintajur Rahman Emon
*/

class Team_Members_Plugin {
    
    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomy'));
        add_shortcode('team_members', array($this, 'team_members_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_load_more_team_members', array($this, 'load_more_team_members'));
        add_action('wp_ajax_nopriv_load_more_team_members', array($this, 'load_more_team_members'));
        add_action('add_meta_boxes', array($this, 'add_team_member_metabox'));
        add_action('save_post', array($this, 'save_team_member_metabox'));
    }
    
    // Register Custom Post Type
    public function register_post_type() {
        $labels = array(
            'name'               => 'Team Members',
            'singular_name'      => 'Team Member',
            'menu_name'          => 'Team Members',
            'name_admin_bar'     => 'Team Member',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Team Member',
            'new_item'           => 'New Team Member',
            'edit_item'          => 'Edit Team Member',
            'view_item'          => 'View Team Member',
            'all_items'          => 'All Team Members',
            'search_items'       => 'Search Team Members',
            'parent_item_colon'  => 'Parent Team Members:',
            'not_found'          => 'No team members found.',
            'not_found_in_trash' => 'No team members found in Trash.'
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'team-member' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' )
        );

        register_post_type( 'team_member', $args );
    }
    
    // Register Custom Taxonomy
    public function register_taxonomy() {
        $labels = array(
            'name'              => 'Member Types',
            'singular_name'     => 'Member Type',
            'search_items'      => 'Search Member Types',
            'all_items'         => 'All Member Types',
            'parent_item'       => 'Parent Member Type',
            'parent_item_colon' => 'Parent Member Type:',
            'edit_item'         => 'Edit Member Type',
            'update_item'       => 'Update Member Type',
            'add_new_item'      => 'Add New Member Type',
            'new_item_name'     => 'New Member Type Name',
            'menu_name'         => 'Member Type',
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'member-type' ),
        );

        register_taxonomy( 'member_type', array( 'team_member' ), $args );
    }
    
    // Shortcode for Displaying Team Members
public function team_members_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts( array(
        'number' => -1,  // Default: Show all team members
        'image_position' => 'top', // Default: Image position on top
        'show_button' => true // Default: Show 'See all' button
    ), $atts );

    // Query team members
    $args = array(
        'post_type' => 'team_member',
        'posts_per_page' => -1, // Retrieve all team members
    );
    $team_members_query = new WP_Query($args);

    // Output
    ob_start(); ?>
    
    <div class="team-members-container">
        <div class="team-members">
            <?php 
            $counter = 0;
            while ($team_members_query->have_posts()) : $team_members_query->the_post(); ?>
                <div class="team-member">
                    <?php
                    // Check image position
                    if ($atts['image_position'] == 'bottom') {
                        // Display image below other details
                        $this->display_member_details();
                        $this->display_member_image();
                    } else {
                        // Display image above other details
                        $this->display_member_image();
                        $this->display_member_details();
                    }
                    ?>
                </div>
                <?php 
                $counter++;
            endwhile; ?>
        </div>

        <?php
        if ($atts['show_button']) {
            echo'<a href="#" class="see-all-button">See All</a>'; // Changed href to "#" temporarily
        }
        wp_reset_postdata();
        ?>
    </div>

    <?php
    return ob_get_clean();
}

// Helper function to display member details
private function display_member_details() {
    ?>
    <div class="member-details">
        <h3><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="member-position"><?php echo get_post_meta(get_the_ID(), 'position', true); ?></div>
        <div class="member-bio"><?php the_content(); ?></div>
    </div>
    <?php
}

// Helper function to display member image
private function display_member_image() {
    ?>
    <div class="member-image">
        <?php
        // Check if the team member has a thumbnail (featured image) set
        if (has_post_thumbnail()) {
            echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail(get_the_ID(), 'thumbnail', array('class' => 'rounded-image')) . '</a>';
        } else {
            // Display default rounded image if no thumbnail is set
            echo '<a href="' . get_permalink() . '"><img src="' . plugins_url( '/assets/images/default-avatar.png', __FILE__ ) . '" alt="Default Avatar" class="rounded-image"></a>';
        }
        ?>
    </div>
    <?php
}


    // Enqueue scripts
    public function enqueue_scripts() {
        wp_enqueue_script('team-members-script', plugins_url('/assets/js/team-members.js', __FILE__), array('jquery'), null, true);
        wp_localize_script('team-members-script', 'team_members_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('team_members_nonce')
        ));
    }

    // AJAX handler to load more team members
    public function load_more_team_members() {
        // Verify nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'team_members_nonce')) {
            wp_die('Permission denied');
        }

        // Offset to determine the number of already loaded team members
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

        // Query additional team members
        $args = array(
            'post_type' => 'team_member',
            'posts_per_page' => 3, // Adjust as needed
            'offset' => $offset
        );
        $team_members_query = new WP_Query($args);

        // Output team members
        if ($team_members_query->have_posts()) {
            while ($team_members_query->have_posts()) : $team_members_query->the_post();
                // Output HTML for additional team members
                $this->display_member_details();
                $this->display_member_image();
            endwhile;
        } else {
            // No more team members to load
            echo 'No more team members';
        }

        wp_die(); // Always die in the end to avoid extra output
    }
    
    // Add meta box for team member image
    public function add_team_member_metabox() {
        add_meta_box(
            'team_member_image',
            'Team Member Image',
            array($this, 'render_team_member_metabox'),
            'team_member',
            'normal',
            'high'
        );
    }
    
    // Render the meta box content
    public function render_team_member_metabox($post) {
        wp_nonce_field('team_member_image_nonce', 'team_member_image_nonce');
        ?>
        <p>
            <label for="team_member_image"><?php _e('Upload Image:', 'team_member_image'); ?></label><br>
            <input type="button" id="upload_team_member_image_button" class="button" value="<?php _e('Upload Image', 'team_member_image'); ?>">
            <input type="hidden" name="team_member_image" id="team_member_image" value="<?php echo esc_attr(get_post_meta($post->ID, 'team_member_image', true)); ?>">
            <br><br>
            <img src="<?php echo esc_attr(get_post_meta($post->ID, 'team_member_image', true)); ?>" id="team_member_image_preview" style="max-width: 200px;">
        </p>
        <?php
    }
    
    // Save meta box data
    public function save_team_member_metabox($post_id) {
        // Check if nonce is set
        if (!isset($_POST['team_member_image_nonce'])) {
            return;
        }
        // Verify nonce
        if (!wp_verify_nonce($_POST['team_member_image_nonce'], 'team_member_image_nonce')) {
            return;
        }
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        // Check the user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        // Save the data
        if (isset($_POST['team_member_image'])) {
            update_post_meta($post_id, 'team_member_image', $_POST['team_member_image']);
        }
    }
}

// Initialize the plugin
$team_members_plugin = new Team_Members_Plugin();

// Enqueue stylesheet
function team_members_styles() {
    wp_enqueue_style( 'team-members-style', plugins_url( '/assets/css/team-members.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'team_members_styles' );

?>
