<?php
// Creating the widget 
class EventM_Venue_Map extends WP_Widget {

    function __construct() {
        parent::__construct('eventm_venue_map',
        EventM_UI_Strings::get("LABEL_VENUE_MAP"),
                array('description' => EventM_UI_Strings::get("LABEL_VENUE_WIDGET_DESC"),)
        );
    }

// Creating widget front-end
// This is where the action happens
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
// before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];
        
        $gmap_api_key= em_global_settings('gmap_api_key');
        if($gmap_api_key):
            em_localize_map_info("em-google-map");
        $events_page_id= em_global_settings("events_page");
        ?>
        <div class="em_widget_container">
            <div id="em_widget_venues_map_canvas" style="height: 300px;"></div>
        </div>
    <script>
        em_jQuery(document).ready(function(){
           em_load_map("venue_widget","em_widget_venues_map_canvas"); 
        });
    </script>
        <?php 
        else:
            echo "Please configure Google Map Api keys";
        endif;
        echo $args['after_widget'];
        
    }

    /**
     * 
     * Widget Backend
     */
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = "New Title";
        }
// Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

// Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

}


// Register and load the widget
function em_load_venue_map() {
    register_widget('eventm_venue_map');
}

add_action('widgets_init', 'em_load_venue_map');
