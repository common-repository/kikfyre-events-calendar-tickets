<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'EventM_calendar' ) ) :
    
class EventM_calendar extends WP_Widget {

    function __construct() {
        parent::__construct('eventm_calendar',
        EventM_UI_Strings::get("LABEL_EVENT_MAGIC_CALENDAR"),
                array('description' => EventM_UI_Strings::get("LABEL_CALENDAR_WIDGET_DESC"),)
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
        
        wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
        $events_page_id= em_global_settings("events_page");
        
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        ?>


        <div class="em_widget_container">
            <a></a>
            <div id="em_calendar_widget">
  </div>

       
        <form name="em_calendar_event_form" method="get" action="<?php echo get_permalink($events_page_id); ?>">
            <input type="hidden" name="em_s"s value="1" />
            <input type="hidden" name="em_sd" id="em_start_date" value="" />
             <div class="em_upcoming_events">
                 <div class="em_calendar_widget-events-title">Upcoming Events</div>
                <?php
                    $event_service= new EventM_Service();
                    $events= $event_service->get_upcoming_events(); 
                     if(!empty($events)):
                        for($i=0;$i< min(5,count($events));$i++){
                ?>
                <div class="em_upcoming_event"><a href="<?php echo get_permalink($events[$i]->ID); ?>">

                    <?php echo $events[$i]->post_title; ?></a>
                </div>
                <?php   }
                    endif;
                ?>
             </div>
        </form>
           
        </div>

   <script type="text/javascript">
  em_jQuery(function(){

    

       // em_change_dp_css();
     
    
  });
  
  em_jQuery(document).on('click', '#em_calendar_widget', function () {
  em_change_dp_css();
})


</script>     
  





        <?php 
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


endif;
// Register and load the widget
function em_load_calendar_widget() {
    register_widget('eventm_calendar');
}

 add_action('widgets_init', 'em_load_calendar_widget');
 
 

