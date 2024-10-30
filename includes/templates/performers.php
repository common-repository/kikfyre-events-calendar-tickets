<?php
    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
    $args = array(
        'orderby' => 'date',
        'order' => 'DESC',
        'posts_per_page' => 10,
        'paged' => $paged,
         'meta_query' => array(	
             'relation' => 'OR',
          array(
             'key' => em_append_meta_key('display_front'),
             'value' => 1,
             'compare' => '='
          ),
	array(
                'key'     => em_append_meta_key('display_front'),
		'value'   => 'true',
                'compare' => '='
            
               )
        ),
        'post_type' => EM_PERFORMER_POST_TYPE);

    $the_query = new WP_Query($args);
    $performers_page_url= get_permalink(em_global_settings("performers_page"));
?>

<?php if ($the_query->have_posts()) : ?>

    <div class="emagic">
        <div class="em_performers dbfl">
         
            <!-- the loop -->
            <?php while ($the_query->have_posts()) : $the_query->the_post();?>
                 <?php $thumbnail_id = get_post_meta(get_the_ID(), '_thumbnail_id', true); ?>
                <div class="em_performer_card difl">
                       <div class="kf-performer-wrap dbfl">
                 
                    <div class="em_performer_image em_block dbfl">
                       
                                <?php  
                            
                                if(!empty($thumbnail_id))
                                {  ?>                               
                                   
                                     <a href="<?php echo add_query_arg("performer",get_the_ID(),$performers_page_url); ?>"><?php echo the_post_thumbnail('thumbnail'); ?></a>
                       <?php   }                         
                                else
                                {?>
                                     <a href="<?php echo add_query_arg("performer",get_the_ID(),$performers_page_url); ?>"><img height="150" width="150" src="<?php echo esc_url( plugins_url( '/images/dummy-performer.png', __FILE__ ) ) ?>" alt="Dummy Image" ></a>
                         <?php  }                              
                                ?>
                        
                    </div>
 					
 					<div class="em_performer_description dbfl">                   
                    	<div class="em_performer_name em_wrap"><a href="<?php echo add_query_arg("performer",get_the_ID(),$performers_page_url); ?>"><?php echo the_title(); ?></a></div>
                        <?php  $role = em_get_post_meta(get_the_ID(), 'role', true);
                            if(!empty($role)){
                            ?>                   
                        <div title="<?php echo $role; ?>" class="em_performer_role em_color em_wrap">
                                <?php echo $role; ?>
                               </div>
                        <?php } ?>

                       <div class="kf_performer_desc em_block"><?php echo the_excerpt(); ?></div>
                    </div> 
                           
                        <?php $event_service = new EventM_Performer_Service();
                        $performer_events = $event_service->get_upcoming_events(get_the_ID()); ?>
                           <div class="kf-performer-card-footer dbfl"><span class="kf-event-counter-title  difr">Events<span class="kf-events-count-wrap em_bg" ><span class="em_events_count_no em_color" ><?php echo count($performer_events);?></span></span>
                </span></div>
                           
                       </div> 
                    
                </div>
            <?php endwhile; 
            ?>
        </div>   
    </div>     

    <?php 
            if ($the_query->max_num_pages > 1) { // check if the max number of pages is greater than 1   ?>
        <nav class="prev-next-posts">
            <div class="prev-posts-link">
                <?php echo get_next_posts_link('Older Entries', $the_query->max_num_pages); // display older posts link  ?>
            </div>
            <div class="next-posts-link">
                <?php echo get_previous_posts_link('Newer Entries'); // display newer posts link  ?>
            </div>
        </nav>
    <?php } ?>

<?php else: ?>
    <article>
       
        <p><?php _e(' No performers found for the listed events.'); ?></p>
    </article>

<?php 
endif; ?>