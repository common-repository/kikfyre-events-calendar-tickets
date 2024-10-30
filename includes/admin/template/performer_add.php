<div class="kikfyre kf-container"  ng-controller="performerCtrl" ng-app="eventMagicApp" ng-cloak ng-init="initialize('edit')">
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>
    <div class="kf-db-content">

            <div class="kf-db-title">
                {{data.trans.heading_new_performer}}
            </div>

            <div class="form_errors">
                 {{formErrors}}   
            </div>
        <!-- FORM -->
        <form name="postForm" ng-submit="savePerformer(postForm.$valid)" novalidate >
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_performer_type}}<sup>*</sup></div>
                <div class="eminput">
                    <ul class="emradio" >
                        <li ng-repeat="(key,value) in data.post.types">
                            <input required type="radio" name="type"  ng-model="data.post.type" value="{{value.key}}"> {{value.label}}
                        </li>
                    </ul>
                    <div class="emfield_error">
                        <span ng-show="postForm.title.$error.required && !postForm.title.$pristine">{{trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote">
                    
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_name}}<sup>*</sup></div>
                <div class="eminput">
                    <input  required  type="text" name="name"  ng-model="data.post.name">
                    <div class="emfield_error">
                         <span ng-show="postForm.name.$error.required && !postForm.name.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Person/Group name who is performing at event. Will be visible on Event page.
                </div>
            </div>
            
            <div class="emrow" ng-show="post_edit">
                <div class="emfield">{{data.trans.label_slug}}<sup>*</sup></div>
                <div class="eminput">
                    <input ng-required="post_edit" type="text" name="slug"  ng-model="data.post.slug" >
                    <div class="emfield_error">
                         <span ng-show="postForm.slug.$error.required && !postForm.slug.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    User friendly URL of performer’s page. Example, /johndoe, /janedoe, etc.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_role}}</div>
                <div class="eminput">
                    <input  type="text" name="role"  ng-model="data.post.role">
                    <div class="emfield_error">
                         
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Performer’s role. Example: Presenter, Stand-up comedian, musician, puppeteer, singer, etc.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_cover_image}}</div>
                <div class="eminput">
                    <input class="kf-upload" type="button" ng-click="mediaUploader(false)" value="{{data.trans.label_upload}}" />
                    <div class="em_cover_image em_gallery_images">
                     <img ng-src="{{data.post.feature_image}}" />
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Cover image of Performer. Will be displayed on Performer page.
                </div>
             </div>
            
          
            <div class="emrow kf-bg-light">
                <div class="emfield emeditor">{{data.trans.label_description}}</div>
                <div class="eminput emeditor">
                    <?php 
                        include_once("editor.php"); 
                        $post_id= event_m_get_param('post_id');
                        $content='';
                        if($post_id!==null && (int)$post_id>0)
                        {
                            $post= get_post($post_id);
                            if(!empty($post))
                                $content= $post->post_content;
                        }
                        em_add_editor('description',$content);
                    ?>
                </div>
                <div class="emnote emeditor">
                    Performer’s details.
                </div>
             </div>
    
             <div class="emrow">
                <div class="emfield">{{data.trans.label_performer_display}}</div>
                <div class="eminput">
                    <input  type="checkbox" name="display_front"  ng-model="data.post.display_front"  ng-true-value="'true'" ng-false-value="'false'">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Hide/Show Performer in Performers directory.
                </div>
             </div>
            
            <input type="text" class="hidden" ng-model="post.feature_image_id" />
            <div class="dbfl kf-buttonarea">
            <div class="em_cancel"><a class="kf-cancel" ng-href="{{data.links.cancel}}">{{data.trans.label_cancel}}</a></div>
            <button type="submit" class="btn btn-primary" ng-disabled="postForm.$invalid || requestInProgress">{{data.trans.label_save}}</button>
            </div>
            <div class="dbfl kf-required-errors" ng-show="postForm.$invalid && postForm.$dirty">
                <h3>Looks like you missed out filling some required fields (*). You will not be able to save until all required fields are filled in. Here’s what's missing - 
                <span ng-show="postForm.type.$error.required">Performer type</span>
                <span ng-show="postForm.name.$error.required">Name</span>
                </h3>
            </div>
        </form>
            
</div>










