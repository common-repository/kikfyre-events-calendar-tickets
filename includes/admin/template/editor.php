<?php
    function em_add_editor($editor_id,$content=''){
      
          wp_editor( $content, $editor_id );
    }