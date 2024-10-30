<?php  
    $event_tour= em_global_settings('event_tour');
?>
    <input type="hidden" value="<?php echo $event_tour ?>" id="em_tour-status">
<div class="kikfyre" ng-app="eventMagicApp" ng-controller="eventCtrl" ng-init="initialize('list')" ng-cloak>
    <div class="kf_progress_screen" ng-show="requestInProgress">
    </div>  
    <div class="kf-operationsbar dbfl">
        <div class="kf-title difl">{{data.trans.heading_events_manager}}</div>
        <div class="kf-icons difr">
        </div>
        <div class="kf-nav dbfl">
            <ul class="dbfl">
                <li><a ng-href="{{data.links.add_new_event}}">{{data.trans.label_add_new}}</a></li>
                <li><button class="em_action_bar_button" ng-click="duplicatePosts()" ng-disabled="selectedAll ||selections.length == 0 ">{{data.trans.label_duplicate}}</button></li>
                <li><button class="em_action_bar_button" ng-click="deletePost()" ng-disabled="selections.length == 0" >{{data.trans.label_delete}}</button></li>
                <li id="em_select_events"><input type="checkbox" id="em_select_all" ng-model="selectedAll" ng-click="checkAll()" ng-true-value='true'/><label for="em_select_all">Select all</label></li>
                <li><input type="checkbox" ng-model="data.hideExpired" ng-click="prepareEventListPage(hideExpired)" ng-true-value="1" id="hide_expired"/><label for="hide_expired">{{data.trans.label_hide_expired}}</label></li>
               <li id="em_take_tour"><button class="em_action_bar_button" ng-click="another_tour()">{{data.trans.label_tour}}</button></li>
                <li class="kf-toggle difr">{{data.trans.label_sort_by}}
                    <select class="kf-dropdown" ng-change="prepareEventListPage(sort_option)" ng-options="sort_option.key as sort_option.label for sort_option in data.trans.sort_options" ng-model="data.sort_option">
                    </select>
                </li>
<!--                <li><button class="em_action_bar_button" ng-click="markAll()"  >{{data.trans.label_mark_all}}</button></li>-->
            </ul>
        </div>

    </div>

    <div class="kf-cards dbfl emagic-event-cards">

        <div class="kf-card difl" ng-repeat="post in data.posts" ng-class="{'emcard-expired':post.is_expired}">
            <div ng-if="post.cover_image_url" class="kf_cover_image dbfl"><img ng-show="post.cover_image_url" ng-src="{{post.cover_image_url}}" /></div>
            <div ng-if="!post.cover_image_url" class="kf_cover_image dbfl"><img  ng-src="<?php echo esc_url(plugins_url('/images/event_dummy.png', __FILE__)) ?>" /></div>

<!--            <div class="cardtitle"><input class="em_card_check" type="checkbox" ng-click="selectPost(post.id)"  ng-true-value="{{post.id}}" ng-false-value="0">{{post.name}}</div>-->
           <div class="kf-card-content dbfl">
               <div class="kf-card-title kf-wrap dbfl" title="{{post.name}}"><input type="checkbox"  ng-model="post.Selected" ng-click="selectPost(post.id)"  ng-true-value="{{post.id}}" ng-false-value="0" id="{{post.name}}"><label for="{{post.name}}">{{post.name}}</label></div>

            <div class="kf-card-info kf-wrap dbfl">{{data.trans.label_at}} 
                <span title="{{post.venue_name}}">
                    {{post.venue_name}}
                </span>
            </div>
            <div class="kf-card-info kf-wrap dbfl em_event_date">{{data.trans.label_on}}  
                <span>
                    {{post.between}}
                </span>
            </div>
            <div class="kf-event-progress dbfl" ng-show="post.capacity>0 && !(post.child_events.length>0)">   
           
            Booked {{post.sum}}/{{post.capacity}}
            <div class="kf-progressbar-bg dbfl" data-sums='{{post.sum}}' data-total='{{post.capacity}}'>
                <div ng-style="getProgressStyle(post)" class="kf-progressbar" ></div>
            </div>

            </div>
            
            <div class="event-progress" ng-show="post.capacity==0 && post.sum>0">   
                Booked {{post.sum}}
            </div>
           
                <div class="kf-card-info"><a ng-href="{{data.links.add_new_event}}&post_id={{post.id}}">Edit</a></div>
            
            </div>
        </div>
        
        <div class="em_empty_card" ng-show="data.posts.length==0 && (data.sort_option == 'date' || data.sort_option == 'title')">
            The Events you create will appear here as neat looking Event Cards. Presently, you do not have any event scheduled.
        </div>
        

    </div>


    <div class="kf-pagination dbfr" ng-show="data.posts.length!=0"> 
        <ul>
             <li class="difr" dir-paginate="post in data.total_posts | itemsPerPage: data.pagination_limit"></li>
        </ul>
         <dir-pagination-controls on-page-change="pageChanged(newPageNumber)"></dir-pagination-controls>
    </div>
</div>

<ol id="em-events-joytips" style="display:none">
      <li><h6>Welcome to Event Kikfyre</h6>
          <hr />
        <p>Event Kikfyre allows you to create and manage events, venues, performers, sell tickets, administer bookings and much more. This little tour will walk you through the basics of events management in Event Kikfyre. You can close the tour anytime by clicking <b>x</b> on top right of this box.</p>
    </li> 
      <li><h6><i class="fa fa-microphone" aria-hidden="true"></i> The Events Manager</h6>
          <hr />
        <p><b>Events Manager</b> is the first page you will see when opening Event Kikfyre. This is where all events you create will display in a grid of <em>event cards</em>. Each card represents a single event. For this demonstration, we have already created a sample event. You are free to create as many events as you like.</p>
    </li>  
    <li data-id="em_add_event" ><h5><i class="fa fa-plus" aria-hidden="true"></i> Add Event</h5>
        <hr />
        <p>You can create new events by clicking here. This will open a new page where you can set event properties. Each property will be accompanied by small help snippets describing its role.</p>
    </li>
    <li data-id="em_duplicate_events" ><h5><i class="fa fa-clone" aria-hidden="true"></i> Duplicate</h5>
        <hr />
        <p>Selecting an event card and clicking this will create an exact duplicate of the event. To make it work, you first have to select an event card.</p>
    </li>
    <li data-id="em_delete_event" ><h5><i class="fa fa-trash" aria-hidden="true"></i> Delete</h5>
        <hr />
        <p>Select and delete an event by clicking here. <em>This action is irreversible!</em> Again, to make it work, you must first select an event card.</p>
    </li>
    <li data-id="em_select_events" ><h5><i class="fa fa-files-o" aria-hidden="true"></i> Select All</h5>
        <hr />
        <p>Want to delete or duplicate all the events in a single go? First select them all from here.</p>
    </li>
    <li data-id="em_hide_events" ><h5><i class="fa fa-calendar-times-o" aria-hidden="true"></i> Hide Expired</h5>
        <hr />
        <p>Hide all events which are now in the past. Helps to keep the space clean.</p>
    </li>
    <li data-id="em_filter_events" data-options="nubPosition:top-right;"><h5><i class="fa fa-sort" aria-hidden="true"></i> Sort Events</h5>
        <hr />
        <p>Sort order of the event cards by capacity filled, event date or alphabetically.</p>
    </li>
      <li data-class="kf-card" ><h5>Event Card</h5>
        <hr />
        <p>This is an event card. Apart from the event name, it also provides other event information at a glance. As you create new events, more event cards will appear in this area. You will also find <b>Venue Cards</b> and <b>Performer Cards</b> in their respective sections. They work similar to how <b>Event Cards</b> work.</p>
    </li>
      <li data-class="kf_cover_image" ><h5><i class="fa fa-picture-o" aria-hidden="true"></i> Featured Image</h5>
        <hr />
        <p>The featured image of the event will appear prominently on the event card. While viewing the event on the front end, feature image will appear on top of the event information depending on the WordPress theme you are using.</p>
    </li>
      <li data-class="kf-card-info" data-options="tipAdjustmentX:93;tipAdjustmentY:48;" ><h5><i class="fa fa-info-circle" aria-hidden="true"></i> Event Information</h5>
        <hr />
        <p>In the bottom half of the event card, below the feature image, you will find event's Name, event's Venue, bookings status, progress bar for booking and an <b>EDIT</b> button to, you guessed it, edit the event. </p>
      </li>
      <li data-class="kf-event-select" ><h5><i class="fa fa-check-square-o" aria-hidden="true"></i> Selecting Event(s)</h5>
        <hr />
        <p>The checkbox besides the event Name will allow you to select an event. You can select multiple events for batch operations; Like duplicating or deleting them.</p>
      </li>
      <li>
          <br>
        <p>Well, this ends the quick overview of Event Manager. This is but a part of what constitutes Event Kikfyre as a whole. As you explore further, you will learn about its other features. You will also find small help snippets besides input boxes to help you understand what they do. Try creating your first event. Good luck!</p>
      </li>
      <li>
          <br>
        <p>And one more thing! If anything does not appears to work as expected (or if you have questions), we're here to help <i class="fa fa-smile-o" aria-hidden="true"></i>. Please write to us <a href="http://directsupport.me">using our helpdesk here</a> and we will try to respond asap.</p>
      </li>
 </ol>

  
    

