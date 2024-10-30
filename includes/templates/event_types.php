<?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
         // Get terms 
         $rows= get_terms(EM_EVENT_TYPE_TAX,
                        array( 'hide_empty' => false,  
                               'paged' => $paged,
                               'offset'=> (int) ($paged-1) * EM_PAGINATION_LIMIT, 
                               'number'=>EM_PAGINATION_LIMIT)
                 );
         $terms= array();
         $type_dao= new EventM_Event_Type_DAO();
         foreach($rows as $tmp){
             $term= new stdClass();
             $term->name= $tmp->name;
             $term->id= $tmp->term_id;
             $term->count=$type_dao->getAttachedEventCount($term->id);
             $term->color=  em_get_term_meta($term->id, 'color', true);
             $terms[]= $term;
         }
         
       
?>

<div class="emagic">
  <?php if (!empty($terms)) { ?>    
<div class="em_cards dbfl em_event_type">

    <?php
        foreach($terms as $term):
    ?>
            <!-- the loop -->
                <?php if($term->count>0)
                 {
                    if($term->color){?>
                       <div class="em_card difl" style="border-bottom:3px solid #<?php echo $term->color; ?>">
                <?php }
                
                    else{?>
                        <div class="em_card difl" style="border-bottom:3px solid #000000 ;">                        
                    <?php } ?>
                            
                            <a href="<?php echo add_query_arg( array('em_s' => '1','em_types'=>$term->id),get_permalink(em_global_settings('events_page'))); ?>">
                                <?php echo $term->name;                       
                                      echo '<span class="em_event_typecount">('.$term->count.')</span>';                     
                                ?>
                           </a>
                     </div>
                <?php
                 }
                else
                {
                     if($term->color){?>
                       <div class="em_card difl" style="border-bottom:3px solid #<?php echo $term->color; ?>">
                <?php }
                
                    else{?>
                        <div class="em_card difl" style="border-bottom:3px solid #000000 ;">                        
                    <?php } 
                    ?>
                            
                    <a> 
                        <?php echo $term->name;                       
                        echo '<span class="em_event_typecount">('.$term->count.')</span>';   ?>
                    </a>
                    </div>
               <?php
                }                
                ?>
              
       

    <?php
        endforeach; 
    ?>
</div>
    <!-- Pagination -->
    <?php
        $total_terms= wp_count_terms(EM_EVENT_TYPE_TAX, array('hide_empty' => false) );
        $pages = ceil($total_terms/EM_PAGINATION_LIMIT);

        // if there's more than one page
        if( $pages > 1 ):
            echo '<ul class="pagination">';

            for ($pagecount=1; $pagecount <= $pages; $pagecount++):
                echo '<li><a href="'.get_permalink().'page/'.$pagecount.'/">'.$pagecount.'</a></li>';
            endfor;

            echo '</ul>';
            
        endif;
    ?>
  <?php }
  else{?>
        <article>       
        <p><?php _e('No Event type found'); ?></p>
    </article>
<?php  }?>
</div>
<script>
    widthAdjust(".em_performer");
    em_jQuery('#addToCalendar').click(function () {

        em_jQuery(this).attr("disabled", true);
    });
    

</script>