<div class="kikfyre" ng-app="eventMagicApp" ng-controller="performerCtrl" ng-init="initialize('list')" ng-cloak>
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>   
    <div class="kf-operationsbar dbfl">
        <div class="kf-title difl">{{data.trans.label_performers}}</div>
        <div class="kf-icons difl">
          
        </div>
        <div class="kf-nav dbfl">
            <ul>
                <li><a ng-href="{{data.links.add_new}}">{{data.trans.label_add_new}}</a></li>
                <!--<li><button class="em_action_bar_button" ng-click="duplicatePosts()" ng-disabled="selections.length == 0">{{data.trans.label_duplicate}}</button></li>-->
                <li><button class="em_action_bar_button" ng-click="deletePosts()" ng-disabled="selections.length == 0" >{{data.trans.label_delete}}</button></li>
                <!--?<li><button class="em_action_bar_button" ng-click="deletePosts()" ng-disabled="!selectedAll && selections.length==0" >{{data.trans.label_delete}}</button></li>-->

                <!--                <li><button class="em_action_bar_button" ng-click="markAll()" >{{data.trans.label_mark_all}}</button></li>-->
                <li> <input type="checkbox" ng-model="selectedAll" ng-click="checkAll()"  ng-checked="selections.length == data.posts.length"id="select_all"/><label for="select_all">Select all</label></li>
            </ul>
        </div>

    </div>
    
    <div class="kf-cards emagic-performers dbfl">

        <div class="kf-card difl" ng-repeat="post in data.posts">
            <div class="kf_cover_image dbfl"><img ng-show="post.cover_image_url" ng-src="{{post.cover_image_url}}" /></div>
<!--            <div class="cardtitle"><input class="em_card_check" type="checkbox" ng-click="selectPost(post.id)"  ng-true-value="{{post.id}}" ng-false-value="0">{{post.name}}</div>-->
           <div class="kf-card-content dbfl">
               <div class="kf-card-title dbfl kf-wrap"><input type="checkbox" ng-model="post.Selected" ng-click="selectPost(post.id)"  ng-true-value="{{post.id}}" ng-false-value="0" id="{{post.name}}"><label for="{{post.name}}">{{post.name}}</label></div>
               <div class="kf-card-info dbfl">
            <div class="kf-per-role"> 
                    {{post.role}}
            </div></div>
            <div class="em_venue_name kf_performer_descp kf-wrap dbfl">
                {{post.short_description}}  
            </div>

           
                <div class="kf-card-info"><a ng-href="{{data.links.add_new}}&post_id={{post.id}}">Edit</a></div>

            </div>
        </div>
        
        <div class="em_empty_card" ng-show="data.posts.length==0">
            The Performer you create will appear here as neat looking Performer Cards. Presently, you do not have any performer created.
        </div>

    </div>
    
    <div class="kf-pagination dbfr" ng-show="data.posts.length!==0"> 
        <ul>
             <li class="difl" dir-paginate="post in data.total_posts | itemsPerPage: data.pagination_limit"></li>
        </ul>
         <dir-pagination-controls on-page-change="pageChanged(newPageNumber)"></dir-pagination-controls>
    </div>
</div>



