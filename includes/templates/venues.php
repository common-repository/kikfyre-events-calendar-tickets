<?php
em_localize_map_info('em-google-map');
$venue_page_id = em_global_settings('venues_page');
$venue_id = (int) event_m_get_param('venue');
if ($venue_id > 0):
    include_once 'taxonomy-em_venue.php';
else:
    ?>
    <div class="emagic">
        <div id="map" class="em_block dbfl">
            <div id="venues_map_canvas" style="height: 400px;"></div>        
        </div>

        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $terms = get_terms('em_venue', array('hide_empty' => false,
            'paged' => $paged,
            'offset' => (int) ($paged - 1) * EM_PAGINATION_LIMIT,
            'order' => event_m_get_param('order'),
            'number' => EM_PAGINATION_LIMIT
                )
        );
        ?>

        <?php if (!empty($terms)) { ?>
        <div class="em_venues em_block dbfl">
        <?php
        
            foreach ($terms as $key => $venue_name):
                ?>           
                    <div class="em_venue_card difl">
                        <div class="kf-venue-wrap dbfl">

                            <div class="em_venue_image em_block dbfl">
                                <!-- Div Display First image  from Gallery of Venues -->
                                <div class="em_venue_image_cover">
            <?php
            $images = em_get_term_meta($venue_name->term_id, 'gallery_images', true);
            if (!empty($images)):
                ?>                     
                                        <a href="<?php echo add_query_arg('venue', $venue_name->term_id, get_permalink($venue_page_id)); ?>"><?php echo wp_get_attachment_image($images[0]); ?></a> 
                                    <?php else: ?>
                                        <a href="<?php echo add_query_arg('venue', $venue_name->term_id, get_permalink($venue_page_id)); ?>">   <img src="<?php echo esc_url(plugins_url('/images/dummy_image_thumbnail.png', __FILE__)) ?>" alt="Dummy Image" ></a>

                                    <?php endif; ?>                        
                                </div>
                            </div>

                            <div class="em_venue_description dbfl">
                                <div class="em_venue_name dbfl em_wrap">
                                    <a href="<?php echo add_query_arg('venue', $venue_name->term_id, get_permalink($venue_page_id)); ?>"><?php echo $venue_name->name . '<br>' ?></a>      
                                    <!--Display Name of Venue -->                     
                                </div>

                                <div class="kf-venue-seating-capacity dbfl em_color">
                                    <?php
                                    $seating_capacity = em_get_term_meta($venue_name->term_id, 'seating_capacity', true); //get seating capacity
                                    $type = em_get_term_meta($venue_name->term_id, 'type', true);

                                    if (!empty($type)) :           // here ve are checking about the type first because in Standing we dont have capacity and in Seat we have capacity                                 
                                        if ($type == 'standings'):
                                            ?>

                                            <div class="kf-event-attr-name em_color dbfl"><?php echo EventM_UI_Strings::get('LABEL_TYPE'); ?></div>
                                            <div class="kf-event-attr-value dbfl"><?php echo EventM_UI_Strings::get('LABEL_STANDINGS'); ?></div>

                                        <?php else: ?>

                                            <div class="kf-event-attr-name em_color dbfl"><?php echo EventM_UI_Strings::get('LABEL_CAPACITY'); ?></div>
                                            <div class="kf-event-attr-value dbfl"> <?php
                                            echo $seating_capacity . ' People ';
                                            //  EventM_UI_Strings::get("LABEL_SEATING_CAPACITY").' '.$seating_capacity.' '.$type; 
                                            ?></div>

                                        <?php
                                        endif;
                                    endif;
                                    ?>
                                </div>
                      <!--<div class="kf-venue-seating-capacity dbfl em_color"> <?php //if($seating_capacity): echo 'Capacity '.$seating_capacity; endif;  ?> </div>-->

                                <div class="em_venue_add dbfl nik">
                                    <?php
                                    $address = em_get_term_meta($venue_name->term_id, 'address', true);
                                    if (!empty($address)):
                                        //echo $address;
                                        echo wp_trim_words($address, 10);
                                        //Display Address of Venue 
                                        $venue_address = $address;
                                    endif;
                                    ?>
                                </div><!-- Performer Description-->
                            </div><!-- em_block_right end-->
                            <?php $venue_service = new EventM_Venue_Service();
                            $upcoming_events = $venue_service->get_upcoming_events($venue_name->term_id);
                            ?>
                            <div class="kf-venue-card-footer dbfl"><span class="kf-event-counter-title  difl"><?php echo EventM_UI_Strings::get('LABEL_UPCOMING_EVENTS'); ?><span class="kf-events-count-wrap em_bg" ><span class="em_events_count_no em_color" ><?php echo count($upcoming_events); ?></span></span>
                                </span>
                                <span class="kf-venue-details difr"><a href="<?php echo add_query_arg('venue', $venue_name->term_id, get_permalink($venue_page_id)); ?>" title="Details"><?php echo EventM_UI_Strings::get('LABEL_DETAILS'); ?></a></span>
                            </div>

                        </div><!-- em_performer end-->



                    </div>
                    <?php
                endforeach;
          
            ?>
        </div>
       
        <?php
        $total_terms = wp_count_terms(EM_EVENT_VENUE_TAX, array('hide_empty' => false));

        $pages = ceil($total_terms / EM_PAGINATION_LIMIT);

        // if there's more than one page
        if ($pages > 1):
            echo '<ul class="pagination">';

            for ($pagecount = 1; $pagecount <= $pages; $pagecount++):
                echo '<li><a href="' . add_query_arg('paged', $pagecount, get_permalink()) . '">' . $pagecount . '</a></li>';
            endfor;

            echo '</ul>';

        endif;
        ?>
 <?php }
 else{ ?>
     <article>
       
        <p><?php _e(' No venues found.'); ?></p>
    </article>
<?php }
 ?>
    </div>
    <script>
        em_jQuery(document).ready(function () {
            em_load_map('venues', 'venues_map_canvas');
            widthAdjust(".em_venue_card");
        });
    </script>   
<?php endif; ?>
