<div class="kikfyre kf-container"  ng-controller="eventTypeCtrl" ng-app="eventMagicApp" ng-cloak ng-init="initialize('edit')">
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>
    <div class="kf-db-content">
        
            <div class="kf-db-title">
                {{data.trans.heading_new_event_type}}
            
        </div>
        <div class="form_errors">
            <ul>
                <li class="emfield_error" ng-repeat="error in  formErrors">
                    <span>{{error}}</span>
                </li>
            </ul>  
        </div>
        <!-- FORM -->
        <form name="termForm" ng-submit="saveEventType(termForm.$valid)" novalidate >
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_name}}<sup>*</sup></div>
                <div class="eminput">
                    <input required type="text" name="name"  ng-model="data.term.name">
                    <div class="emfield_error">
                        <span ng-show="termForm.name.$error.required && !termForm.name.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Name the Event type.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_color}}</div>
                <div class="eminput">
                    <input id="em_color_picker" class="jscolor"  type="text" name="color"  ng-model="data.term.color" >
                    <div class="emfield_error">
                        
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Color sprite to identify Event type.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_age_group}}</div>
                <div class="eminput">
                    <select  ng-model="data.term.age_group" name="age_group" ng-options="group.key as group.label for group in data.term.age_groups">
                    </select>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Valid age group for the Event. This will be displayed on Event page.
                </div>
            </div>
            
             
            <div class="emrow" ng-show="data.term.age_group=='custom_group'">
                <div class="emfield">{{data.trans.label_custom_age}}</div>
                <div class="eminput">
                    <input id="custom_group" type="text" name="custom_group" ng-model="data.term.custom_group" >
                </div>
<!--                <div class="emfield_error">
                        <span ng-show="termForm.custom_group.$error.required && !termForm.custom_group.$pristine">{{data.trans.validation_required}}</span>
                    </div>-->
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Enter Age advisory for this type for example Age should be between 21-28.
                </div>
            </div>
            
            
            <div class="emrow kf-bg-light">
                <div class="emfield emeditor">{{data.trans.label_special_instructions}}</div>
                <div class="eminput emeditor">
                    <?php 
                            include_once("editor.php"); 
                            $term_id= event_m_get_param('term_id');
                            $content='';
                            if($term_id!==null && (int)$term_id>0)
                            {

                                $term= get_term($term_id);
                                
                                $content= em_get_term_meta($term->term_id, 'description', true); 
                                

                            }
                            
                            em_add_editor('description',$content);
                    ?>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote emeditor">
                    Name of your form. This is not visible on front-end
                </div>
            </div>
            
             <div class="dbfl kf-buttonarea">
            <div class="em_cancel"><a class="kf-cancel" ng-href="{{data.links.cancel}}">{{data.trans.label_cancel}}</a></div>
            <button type="submit" class="btn btn-primary" ng-disabled="termForm.$invalid || requestInProgress">{{data.trans.label_save}}</button>
            <span class="kf-error" ng-show="termForm.$invalid && termForm.$dirty ">Please fill all required fields.</span>
            </div>
        
            <div class="dbfl kf-required-errors" ng-show="termForm.$invalid && termForm.$dirty">
                <h3>Looks like you missed out filling some required fields (*). You will not be able to save until all required fields are filled in. Here’s what's missing - 
                
                <span ng-show="termForm.name.$error.required">Name</span>
                </h3>
            </div>
        
    </form>
            
            
</div>
</div>









