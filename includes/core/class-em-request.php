<?php

interface EventM_Request
{
    function map_request_to_model($type= null);
    function get_param($param = null, $secure = false);
    function get_data();
}
