<div class="kikfyre" ng-app="eventMagicApp" ng-controller="bookingCtrl" ng-cloak="" ng-init="initialize('list')">
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>
    <!-----Operations bar Starts-->
    
    <div class="kf-operationsbar dbfl">
        <div class="kf-title difl">{{data.trans.heading_booking_manager}}</div>

        <div class="kf-nav dbfl">
            <ul>
              
                 <li>
                     <form action="admin-ajax.php?action=em_export_bookings" method="post" name="em_booking_export">
                          <a ng-click="prepare_export_link(true)" class="export_data" >{{data.trans.label_export_all}}</a>
                          <input type="hidden" name="post_query" id="em_post_query" />
                          <input type="hidden" name="selected_bookings" id="em_selected_bookings">
                         
                     </form>
                    
                 </li>


                <li><button ng-disabled="selections.length == 0"  ng-click="deletePosts()">{{data.trans.label_delete}}</button></li>

                <li class="kf-toggle difr">
                    {{data.trans.label_displaying_for}}
                    <select class="kf-dropdown" id="em_form_dropdown" ng-change="filter()" name="event" ng-model="data.event" 
                            ng-options="event.id as event.title for event in data.events">
                    </select>
                </li>
            </ul>
        </div>
    </div>
    <!--  Operations bar Ends----->


    <!-------Content area Starts----->
    <div class="emagic-table dbfl">
        <div class="kf-sidebar difl" ng-disabled="paged>1">
            <div class="kf-filter dbfl">
                <?php echo EventM_UI_Strings::get("LABEL_TIME"); ?>
                <div class="filter-row dbfl" ng-click="filter()"><input type="radio"  name="filter_between" value="all" ng-click="filter()" ng-model="data.filter_between">{{data.trans.label_all}}</div>
                <div class="filter-row dbfl" ><input type="radio" ng-click="filter()" ng-model="data.filter_between"  name="filter_between" value="today">{{data.trans.label_today}}</div>
                <div class="filter-row dbfl" ng-click="filter()"><input type="radio" ng-model="data.filter_between" name="filter_between" value="week"  >{{data.trans.label_this_week}}</div>
                <div class="filter-row dbfl" ng-click="filter()"><input type="radio" ng-model="data.filter_between" name="filter_between" value="month" >{{data.trans.label_this_month}}</div>
                <div class="filter-row dbfl" ng-click="filter()"><input type="radio" ng-model="data.filter_between" name="filter_between" value="year"  >{{data.trans.label_this_year}}</div>
                <div class="filter-row dbfl"><input type="radio" ng-model="data.filter_between" name="filter_between" value="range"  >{{data.trans.label_specific_period}}</div>
                <div id="date_box" ng-show="showDates">
                    <div class="filter-row dbfl"><span>{{data.trans.label_from}}</span><input type="text"   id="em_date_from" name="date_from" ng-model="data.date_from"></div>
                    <div class="filter-row dbfl"><span>{{data.trans.label_to}}</span> <input type="text"   id="em_date_to" name="date_to" ng-model="data.date_to"></div>
                    <div class="filter-row dbfl"><input class="kf-upload" type="button" ng-click="filter()" value="{{data.trans.label_search}}"></div>
                </div>
                <div class="filter-row dbfl">
                    {{data.trans.label_booking_status}}
                    <select class="dbfl" id="filter_status" ng-model="data.filter_status" ng-change="prepareListPage()" name="filter_status" 
                            ng-options="status.key as status.label for status in data.status">
                    </select>    
                </div>
                
                <div class="filter-row">		
                                       <input type="button" value="Reset" onclick="location.reload()" class="btn btn-primary kf-upload" />  		
               </div>
                
            </div>


        </div>

        <!--*******Side Bar Ends*********-->
        <table class="kf-table difl">
            <tr>
                <th><input type="checkbox" id="em_bulk_selector" ng-click="markAll()" ng-model="selectedAll"  ng-checked="selections.length == data.posts.length"/></th>
                <th>{{data.trans.label_booking_id}}</th>
                <th>{{data.trans.label_username}}</th>
                <th>{{data.trans.label_email}}</th>
                <th>{{data.trans.label_no_tickets}}</th>
                <th>{{data.trans.label_actions}}</th>
            </tr>

            <tr ng-repeat="post in data.posts">
                <td><input ng-click="updateSelection(post.id)" class="em_card_check" type="checkbox" ng-model="post.selected" ng-true-value="{{post.id}}" ng-false-value="0"  id="{{post.id}}"/></td>
                <td><label for="{{post.id}}">{{post.id}}</label></td>
                <td>{{post.user_display_name}}</td>
                <td>{{post.user_email}}</td>
                <td>{{post.no_tickets}}</td>
                <td><a href="{{data.links.edit}}&post_id={{post.id}}">{{data.trans.label_view}}</a></td>
            </tr>
        </table>
        </form>

        <div class="em_empty_card" ng-hide="data.posts.length>0">
            {{data.trans.label_no_booking_records}} 
        </div>

    </div>

    <div class="em_pagination" ng-show="data.posts.length !== 0"> 
       <div class="kf-pagination dbfr"> 
            <ul>
                <li dir-paginate="post in data.total_bookings | itemsPerPage: data.pagination_limit" current-page="data.paged"></li>
            </ul>
            <dir-pagination-controls on-page-change="pageChanged(newPageNumber)"></dir-pagination-controls>
       </div>
    </div>

</div>

<script>
    jQuery(document).ready(function () {
        jQuery("#em_date_from").datepicker(
                {
                    onSelect: function (date) {

                        var selectedDate = new Date(date);
                        var msecsInADay = 86400000;
                        var endDate = new Date(selectedDate.getTime() + msecsInADay);

                        jQuery("#em_date_to").datepicker("option", "minDate", endDate);

                    }
                });

    
    jQuery("#em_date_to").datepicker(
            {
                onSelect: function (date) {

                    var selectedDate = new Date(date);
                    var msecsInADay = 86400000;
                    var endDate = new Date(selectedDate.getTime() + msecsInADay);

                    jQuery("#em_date_from").datepicker("option", "maxDate", endDate);

                }

    });
 
 });   
</script>

