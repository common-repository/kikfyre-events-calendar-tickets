<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('EventM_filter')) :

// Creating the widget 
    class EventM_filter extends WP_Widget {

        function __construct() {
            parent::__construct('eventm_filter', EventM_UI_Strings::get("LABEL_EVENT_MAGIC_VENUE_FILTER"), array('description' => EventM_UI_Strings::get("LABEL_VENUE_FILTER_WIDGET_DESC"),)
            );
        }

// Creating widget front-end
// This is where the action happens
        public function widget($args, $instance) {
            wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
            $events_page_id= em_global_settings("events_page");
            $form_action= !empty($events_page_id)?get_permalink($events_page_id):"";
            ?>
            <div class="widget em_widget_container em_widget_filter">
                <form id="em_event_search_form" method="POST" name="em_event_search_form" action="<?php echo $form_action; ?>">
                    <div class="em_widget_search">
                       <label class='em_widget_label dbfl'>Search Events</label>
                       <input type="hidden" name="em_s" value="1" />
                       <input class="em_input" type="text" name="em_search" id="em_search" value="<?php  $data=event_m_get_param('em_search'); echo $data; ?>" />
                    </div>
                    
                    <a id="hide_filter" >View and Hide Filter</a>
                    <div class="kf-event-search-filter">
                    <div class="kf-event-types">
                        <?php
                        echo "<label class='em_widget_label'>".EventM_UI_Strings::get("LABEL_HEADING_EVENT_TYPE_MANAGER")."</label>";
                        $eventType_service = new EventTypeM_Service();
                        $events_types = $eventType_service->get_all_Event_type();
                        $em_types= (array) event_m_get_param('em_types'); 
                        if (count($events_types) > 0):
                            foreach ($events_types as $type):
                                $color = em_get_term_meta($type->term_id, 'color', true);
                            ?>
                            <div class="kf-event-type">
                                <div class="em_radio">
                                    <input type="checkbox" class="em_type_option" name="em_types[]" value="<?php echo $type->term_id; ?>" <?php  if(in_array($type->term_id,$em_types)) echo 'checked'; ?> id="<?php echo $type->term_id; ?>"style="background-color: <?php echo '#'.$color; ?>" />
                                	<?php echo $type->name; ?></div>
                            </div>
                                <?php
                            endforeach;
                        endif;
                        ?>
                        <script>
                             em_jQuery(function () {
                                   em_jQuery('#em_event_search_form').on('reset', function () {
                                        em_jQuery(".em_type_option").each(function(){
                                            em_jQuery(this).attr('checked',false);
                                        });
                                      });                                        
                                   });
                                   
                                      
                                       
                        </script>       
                    </div>

                    <div class='start-end-date'>
                        
                        <div class="kf-widget-label"> <?php echo EventM_UI_Strings::get("LABEL_DATE"); ?></div>
                        <div class="kf_widget-input-group">
                           <!-- <label class="kf_widget_label" for="em_from_date"><div class="kf_widget-input-addon"><i class="material-icons">date_range</i></div></label>-->
                        <input readonly  class="reset" id="em_from_date" name="em_sd" value="<?php  $data=event_m_get_param('em_sd'); echo $data; ?>"/>
                        </div>
                        
                        <a id="reset_date" class="reset" >Clear</a>
                    </div>
            

                        <script>
                            em_jQuery(function () {

                                var em_dateformat = "<?php echo EventM_Utility::dateformat_PHP_to_jQueryUI(get_option('date_format')); ?>"


                                        em_jQuery("#em_from_date:first").datepicker({
                                            dateFormat: em_dateformat
                                        });
                                
                             });
                             
                                em_jQuery('#reset_date').click(function(){
                                          
                                                var input = em_jQuery(this).prev();
                                                em_jQuery("#em_from_date").val(''); // assuming you want it reset to an empty state;
                                            });
                            
                        </script>

                    <div class='kf-widget-venue-filter'>
                        <div class="kf-widget-label"><?php echo EventM_UI_Strings::get("LABEL_VENUE"); ?></div>

                         <div class="kf_widget-input-group">
                             <!---<label class="em_widget_label" for="em_venues">
                                <div class="kf_widget-input-addon"><i class="material-icons">keyboard_arrow_down</i></div>
                                </label>-->

                            <?php
                            $venue_service = new EventM_Venue_Service();
                            $all_venues = $venue_service->get_venues();
                            //echo '<pre>';print_r($all_venues);
                            if (count($all_venues) > 0):
                                ?>
                                <select name="em_venues" id="em_venues">
                                     <option value="">Select Venues</option>
                                    <?php foreach ($all_venues as $venues): ?>
                                        <option  value='<?php echo $venues->term_id; ?>'><?php echo $venues->name; ?></option>
                                <?php endforeach;
                                ;
                                ?>
                                </select>
                    <?php endif; ?>
                            
                            <script type="text/javascript">
                                document.getElementById('em_venues').value = "<?php  $data=event_m_get_param('em_venues'); echo $data; ?>";
                            </script>
                            
                        </div>
                    
                    </div>
                   
                    </div>
                     <div class="em_widget_search_buttons">    
                        <input type="submit" id="searchsubmit" value="Search" class="em_color" />
                       
                          <button type="button" id="configreset" value="Reset" class="em_color" > Reset </button>             
                        <!--<a href="<?php //the_permalink(em_global_settings("events_page"));  ?>">reset</a>-->                    
                       
                    </div>
                </form> 
            </div>
<script>
 em_jQuery('#configreset').click(function(){
            em_jQuery('#em_event_search_form')[0].reset();
           
             window.location.href = '<?php the_permalink(em_global_settings("events_page"));  ?>';
         
 });
 
  em_jQuery("#hide_filter").click(function(){
                                            em_jQuery(".kf-event-search-filter").toggle();
                                        });
</script>
            <?php
        }

    }

    endif;

// Register and load the widget
function em_load_event_filter_widget() {
    register_widget('eventm_filter');
}

add_action('widgets_init', 'em_load_event_filter_widget');


