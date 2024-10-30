<div class="kikfyre" ng-app="eventMagicApp" ng-controller="eventTicketCtrl" ng-init="initialize('list')" ng-cloak>
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>     
    <div class="kf-operationsbar dbfl">
        <div class="kf-title difl">{{data.trans.heading_ticket_manager}}</div>
        <div class="kf-icons difr">
           
        </div>
        <div class="kf-nav dbfl">
            <ul>
                <li><a class="em_action_bar_button" ng-href="{{data.links.add_new}}">{{data.trans.label_add_new}}</a></li>
                <li><button class="em_action_bar_button" ng-click="duplicatePosts()" ng-disabled="selections.length == 0" >{{data.trans.label_duplicate}}</button></li>
                <li><button class="em_action_bar_button" ng-click="deletePosts()" ng-disabled="selections.length == 0" >{{data.trans.label_delete}}</button></li>          
            </ul>
        </div>

    </div>

    <div class="emagic-table dbfl">
                <table class="kf-tickets-table"><!-- remove class for default 80% view -->
                    <tr>
                        <th class="table-header"><input type="checkbox" ng-model="selectedAll" ng-click="markAll()" /></th>
                        <th class="table-header">Name</th>
                        <th class="table-header">Action</th>
                    </tr>
                    
                    <tr ng-repeat="post in data.posts">
                        <td>
                            <input type="checkbox"  ng-model="post.Selected" ng-click="selectPost(post.id)"  ng-true-value="{{post.id}}" ng-false-value="0" id="{{post.id}}"><label for="{{post.id}}">{{post.name}}</label>
                        </td>
                        <td>{{post.name}}</td>
                        <td>
                            <a ng-href="{{data.links.add_new}}&post_id={{post.id}}">View/Edit</a>
                        </td>
                    </tr>
                </table>
            </form>
            
            <div class="em_empty_card" ng-show="data.posts.length==0">
                Ticket Template you create will appear here in tabular Format . Presently, you do not have any Ticket Template created.
            </div>
            
    </div>
    
    
    <div class="kf-pagination dbfr" ng-show="data.posts.length!==0"> 
        <ul>
             <li dir-paginate="post in data.total_posts| itemsPerPage: data.pagination_limit"></li>
        </ul>
         <dir-pagination-controls on-page-change="pageChanged(newPageNumber)"></dir-pagination-controls>
    </div>
</div>



