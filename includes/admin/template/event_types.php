<div class="kikfyre" ng-app="eventMagicApp" ng-controller="eventTypeCtrl" ng-cloak="" ng-init="initialize('list')">
    <!-- Operations bar Starts -->
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>  
    <div class="kf-operationsbar dbfl">
        <div class="kf-title difl">{{data.trans.heading_event_types}}</div>
        <div class="kf-icons difr">
            
        </div>
        <div class="kf-nav dbfl">
            <ul>
                <li><a ng-href="{{data.links.add_new}}">{{data.trans.label_add_new}}</a></li>
                <li><button class="em_action_bar_button" ng-click="deleteTerms()" ng-disabled="selections.length == 0" >{{data.trans.label_delete}}</button></li>

            </ul>
        </div>

    </div>
    <!--  Operations bar Ends -->


    <!-- Content area Starts -->
        <div class="emagic-table dbfl">
                <table class="kf-etypes-table"><!-- remove class for default 80% view -->
                    <tr>
                        <th class="table-header"><input type="checkbox" id="em_bulk_selector" ng-click="checkAll()" ng-model="selectedAll"  ng-checked="selections.length == data.terms.length"/></th>
                        <th class="table-header">Name</th>
                        <th class="table-header">Color Sprite</th>
                        <th class="table-header">Action</th>
                    </tr>
                    
                    <tr ng-repeat="term in data.terms">
                        <td>
                            <input type="checkbox"  ng-model="term.Selected" ng-click="selectTerm(term.id)"  ng-true-value="{{term.id}}" ng-false-value="0">
                        </td>
                        <td>{{term.name}}</td>
                        <td><span class="color-block" ng-style="bgColor(term)"></span></td>
                        <td>
                            <a ng-href="{{data.links.add_new}}&term_id={{term.id}}">View/Edit</a>
                        </td>
                    </tr>
                </table>
            </form>
            
            <div class="em_empty_card" ng-show="data.terms.length==0">
               The Event Types you create will appear here in tabular Format . Presently, you do not have any event type created.
            </div>
            
        </div>
    
    <div class="kf-pagination dbfr" ng-show="data.terms.length!=0"> 
        <ul>
             <li dir-paginate="term in data.total_count | itemsPerPage: data.pagination_limit"></li>
        </ul>
         <dir-pagination-controls on-page-change="pageChanged(newPageNumber)"></dir-pagination-controls>
    </div>
    
</div>



