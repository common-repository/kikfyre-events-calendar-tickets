<div class="kikfyre" ng-app="eventMagicApp" ng-controller="venueCtrl" ng-init="initialize('list')" ng-cloak>
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>   
    <div class="kf-operationsbar dbfl">
        <div class="kf-title difl">{{data.trans.heading_venue_manager}}</div>
        <div class="kf-icons difl">
            
        </div>
        <div class="kf-nav dbfl">
            <ul class="dbfl">
                <li><a ng-href="{{data.links.add_new}}">{{data.trans.label_add_new}}</a></li>
                <li><button class="em_action_bar_button" ng-click="deleteTerms()" ng-disabled="selections.length == 0" >{{data.trans.label_delete}}</button></li>
                <li> <input type="checkbox" ng-model="selectedAll" ng-click="checkAllVENUES()" ng-checked="selections.length == data.terms.length" id="select_all"/><label for="select_all">Select All</label></li>
                <li class="em-form-toggle difr">{{data.trans.label_sort_by}}
                    <select class="kf-dropdown" ng-change="prepareVenueListPage(sort_option)" ng-options="sort_option.key as sort_option.label for sort_option in data.sort_options" ng-model="data.sort_option">
                    </select>
                </li>
<!--                <li><button class="em_action_bar_button" ng-click="markAll()">{{data.trans.label_mark_all}}</button></li>-->
            </ul>
        </div>

    </div>

    <div class="kf-cards emagic-venue-cards dbfl">

        <div class="kf-card difl" ng-repeat="term in data.terms">
            <div ng-if="term.feature_image" class="kf_cover_image dbfl"><img ng-show="term.feature_image" ng-src="{{term.feature_image}}" /></div>
            <div ng-if="!term.feature_image" class="kf_cover_image dbfl"><img ng-src="<?php echo esc_url(plugins_url('/images/event_dummy.png', __FILE__)) ?>" /></div>
<!--            <div class="cardtitle"><input type="checkbox" class="em_card_check" ng-click="selectTerm(term.id)"  ng-true-value="{{term.id}}" ng-false-value="0">{{term.name}}</div>-->
            <div class="kf-card-content dbfl">
                <div class="kf-card-title kf-wrap dbfl" title="{{term.name}}"><input type="checkbox"  ng-model="term.Selected" ng-click="selectTerm(term.id)"  ng-true-value="{{term.id}}" ng-false-value="0" id="{{term.name}}"><label for="{{term.name}}">{{term.name}}</label></div>
   
            <div class="kf_venue_seats kf-wrap dbfl">  
                <span class="kf_upcoming_count" ng-show="term.seating_capacity !=null">{{term.seating_capacity}} {{data.trans.label_seats}}</span>
                <span class="kf_upcoming_count" ng-show="term.seating_capacity ==null">{{data.trans.label_standing}}</span>
            </div>
            
            <div class="kf_upcoming">  
                Event(s) <span class="kf_upcoming_count">{{term.event_count}}</span>
            </div>
            
            <div class="em_venue_address kf-wrap" title="{{term.address}}">  
                {{term.address}}
            </div>

            
                <div class="kf-card-info dbfl"><a ng-href="{{data.links.add_new}}&term_id={{term.id}}">Edit</a></div>

            
            </div>
    
        </div>
        
        <div class="em_empty_card" ng-show="data.terms.length==0">
             The Venue you create will appear here as neat looking Venue Cards. Presently, you do not have any Venue created.
        </div>
    </div>
    
    
    <div class="kf-pagination dbfr" ng-show="data.terms.length!=0"> 
        <ul class="empagination">
             <li class="difl" dir-paginate="term in data.total_count | itemsPerPage: data.pagination_limit"></li>
        </ul>
         <dir-pagination-controls on-page-change="pageChanged(newPageNumber)"></dir-pagination-controls>
    </div>
</div>



