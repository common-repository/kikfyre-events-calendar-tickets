<div class="kikfyre" ng-app="eventMagicApp" ng-controller="analyticCtrl" ng-init="initialize()" ng-cloak="">
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>
    <div class="kf-titlebar_dark dbfl">
        <div class="kf-title kf-title-1 difl">{{data.trans.label_analytics}}</div>
    </div>
    <form  class="em-filters-form dbfl" name="filterForm" novalidate >
        
        <div class="kf-filter-1 em-filter-bar dbfl">
            <div>
                <select class="kf-filter-bar kf-dropdown difl" name="report_by" ng-model="data.filter_type" ng-change="showFilters()">
                <option value="">Select type</option>
                <option value="revenue">Revenue</option>
                <option value="booking">Booking</option>
            </select>
        </div>
        </div>    
        <div class="filters em-filter-bar dbfl" ng-show="showFilterOptions">
            <div>
                <select class="kf-dropdown difl" name="report_by" ng-model="data.report_by" required>
                    <option value="today" selected>Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="this_week">This Week</option>
                    <option value="last_week">Last Week</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_year">This Year</option>
                    <option value="last_year">Last Year</option>
                    <option value="custom">Custom</option>
                </select>
            </div>
        
            <div>
                <select class="kf-dropdown difl" name="venue" ng-model="data.venue" ng-options="venue.id as venue.name for venue in data.venues ">
                    
                </select>
            </div>
        
            <div>
                <select class="kf-dropdown difl" name="event" ng-model="data.event" ng-options="event.id as event.name for event in data.events ">
                    
                </select>
            </div>
                
           <div ng-show="data.report_by=='custom'" class="dbfl">
                <div class="difl kf-custom-date">
                    <span class="difl">Start Date</span>
                    <div><input class="difl" type="text" ng-required="data.report_by=='custom'" id="start_date" name="start_date" ng-model="data.start_date" /></div>
                </div>
                <div class="difl kf-custom-date">
                    <span class="difl">End Date</span>
                    <div><input class="difl" type="text" ng-required="data.report_by=='custom'" id="end_date" name="end_date" ng-model="data.end_date" /></div>
                </div>
            </div>
            
        <div class="kf-filter-button">		
            <input type="button" value="Reset" onclick="location.reload()" class="btn btn-primary kf-upload" />  		
 -      </div>
            
        <div class="kf-filter-button">
            <input type="button" class="btn btn-primary kf-upload" ng-disabled="filterForm.$invalid" value="Filter" ng-click="getData()" />
        </div>    
    </div>
   </form>
    
 
    <div id="revenue_chart" class="dbfl" style="width: 900px; height: 500px"></div>
    <div id="booking_chart" class="dbfl" style="width: 900px; height: 500px"></div>
</div>


