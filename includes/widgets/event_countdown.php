<?php
// Creating the widget 
if(!class_exists('EventM_Event_Countdown')):
class EventM_Event_Countdown extends WP_Widget {

    function __construct() {
        parent::__construct('eventm_event_countdown',
        "Event Countdown",
                array()
        );
    }

// Creating widget front-end
// This is where the action happens
    public function widget($args, $instance) { 
        $title = apply_filters('widget_title', $instance['title'],$instance['event_id']);
// before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];
        $events_page_id= em_global_settings("events_page");
        
        $event_id= (int) $instance['event_id'];
        $event= get_post($event_id);
        
        if(!empty($event)):
            $start_date= em_get_post_meta($event_id, 'start_date', true);
            $hide_from_events= em_get_post_meta($event_id, 'hide_event_from_events', true);
        ?>
       
        <?php 
            if($start_date>current_time('timestamp') && $hide_from_events!=1):  ?>
<div class="event_title dbfl"><a href="<?php echo add_query_arg('event',$event_id,get_permalink($events_page_id)); ?>"><?php echo $event->post_title; ?></a></div> 
          <?php  $start_date= em_showDateTime($start_date,true);
            wp_enqueue_script( "em_countdown_jquery",plugin_dir_url(__DIR__) . 'templates/js/jquery.countdown.min.js',false );
        ?>
            <div class="em_widget_container">
                <div class="em_countdown_timer dbfl" id="em_widget_event_countdown_<?php echo $this->number; ?>">
                      <span class="days em_color" id="em_countdown_days_<?php echo $this->number; ?>"></span>
                      <span class="hours em_color" id="em_countdown_hours_<?php echo $this->number; ?>"></span>
                      <span class="minutes em_color" id="em_countdown_minutes_<?php echo $this->number; ?>"></span>
                      <span class="seconds em_color" id="em_countdown_seconds_<?php echo $this->number; ?>"></span>
                </div>
            </div>
            <script type="text/javascript">
              em_jQuery(document).ready(function(){
                  var date = new Date("<?php echo $start_date; ?>");
                  console.log(date);
                  em_jQuery('#em_widget_event_countdown_<?php echo $this->number; ?>').countdown(date, function(event) {
                  em_jQuery("#em_countdown_days_<?php echo $this->number; ?>").html(event.strftime('%D')); 
                  em_jQuery("#em_countdown_hours_<?php echo $this->number; ?>").html(event.strftime('%H'));
                  em_jQuery("#em_countdown_minutes_<?php echo $this->number; ?>").html(event.strftime('%M'));
                  em_jQuery("#em_countdown_seconds_<?php echo $this->number; ?>").html(event.strftime('%S'));
                //  em_jQuery(this).html(event.strftime('%w weeks %d days %H:%M:%S'));
              });
                  var emDominentColor = jQuery('.event_title').prepend('<a></a>');
                  var emColor = jQuery('.event_title').find('a').css('color');
                  jQuery(".em_color").css('color', emColor);
                  jQuery(".em_bg").css('background-color', emColor);
              });
            </script>
        <?php 
            endif;
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
        
        if (isset($instance['event_id'])) {
            $event_id = $instance['event_id'];
        } else {
            $event_id = "";
        }
// Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        
        <p>
            <label for="event">Event</label> <br>
            <?php
                    $event_service= new EventM_Service();
                    $events= $event_service->get_upcoming_events();
            ?>
            <select id="<?php echo $this->get_field_id('event_id'); ?>" name="<?php echo $this->get_field_name('event_id'); ?>">
                <option>Select Event</option>
                <?php
                    if(!empty($events)):
                        foreach($events as $event):
                            $event_start_date=em_get_post_meta($event->ID, 'start_date', true);
                            if($event_start_date<=current_time('timestamp'))
                                continue;
                ?>
                <option <?php if($event_id==$event->ID) echo 'selected'; ?> value="<?php echo $event->ID ?>"><?php echo $event->post_title; ?></option>    
                <?php 
                      endforeach;  
                    endif;
                ?>
            </select>
        </p>
        <?php
    }

// Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
       
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['event_id'] = (!empty($new_instance['event_id']) ) ? strip_tags($new_instance['event_id']) : '';
        return $instance;
    }
    
 

}

endif;
// Register and load the widget
function em_load_event_countdown() {
    register_widget('eventm_event_countdown');
}

add_action('widgets_init', 'em_load_event_countdown');
