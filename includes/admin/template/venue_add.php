<?php 
        $gmap_api_key= em_global_settings('gmap_api_key');
        if($gmap_api_key)
             wp_enqueue_script ('google_map_key', 'https://maps.googleapis.com/maps/api/js?key='.$gmap_api_key.'&libraries=places');
?>


<div class="kikfyre kf-container"  ng-controller="venueCtrl" ng-app="eventMagicApp" ng-init="initialize('edit')" ng-cloak>
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>
    <div class="kf-db-content">
            <div class="kf-title">
                {{data.trans.heading_venue_page}}
            </div>

        <div class="form_errors">
            <ul>
                <li class="emfield_error" ng-repeat="error in  formErrors">
                    <span>{{error}}</span>
                </li>
            </ul>  
        </div>
        
        <div class="em_notice">
            <div class="map_notice" ng-show="!data.term.map_configured">
                {{data.term.map_notice}}
            </div>
        </div>
        <!-- FORM -->
        <form name="termForm" ng-submit="saveTerm(termForm.$valid)" novalidate >

            <div class="emrow">
                <div class="emfield">{{data.trans.label_name}}<sup>*</sup></div>
                <div class="eminput">
                    <input placeholder="" required type="text" name="name"  ng-model="data.term.name">
                    <div class="emfield_error">
                        <span ng-show="termForm.name.$error.required && !termForm.name.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Name of the Venue. Should be unique.
                </div>
            </div>

            <div class="emrow" ng-show="term_edit" style="display:none;">
                <div class="emfield">{{data.trans.label_slug}}<sup>*</sup></div>
                <div class="eminput">
                    <input ng-required="term_edit" type="text" name="slug"  ng-model="data.term.slug">
                    <div class="emfield_error">
                        <span ng-show="termForm.slug.$error.required && !termForm.slug.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Slug is the user friendly URL for the Venue. Example: /minnesotagrounds,/sydneyolympicpark, /millersstadium etc.
                </div>
            </div>

            <div class="emrow kf-bg-light">
                <div class="emfield emeditor">{{data.trans.label_description}}</div>
                <div class="eminput emeditor">
                    <?php 
                          include("editor.php"); 
                          $id= event_m_get_param('term_id');
                          $content='';
                          if($id!==null && (int)$id>0)
                            {

                                $term= get_term($id);
                                $content= em_get_term_meta($id, 'description',true);
                           }
                           em_add_editor('description',$content);
                    ?>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote emeditor">
                    Details about the Venue. Will be displayed on Event and Venue page.
                </div>
            </div>

            <div class="emrow kf-bg-light" ng-show="data.term.map_configured">
                <div class="emfield emeditor">{{data.trans.label_address}}</div>
                <div class="eminput emeditor">
                    <input id="em-pac-input" name="address" ng-model="data.term.address" class="em-map-controls" type="text" >
                    <div id="map"></div>
                    <div id="type-selector" class="em-map-controls" style="display:none">
                        <input type="radio" name="type" id="changetype-all" checked="checked">
                        <label for="changetype-all">{{data.trans.label_gmap_control_all}}</label>

                        <input type="radio" name="type" id="changetype-establishment">
                        <label for="changetype-establishment">{{data.trans.label_gmap_control_est}}</label>

                        <input type="radio" name="type" id="changetype-address">
                        <label for="changetype-address">Addresses</label>

                        <input type="radio" name="type" id="changetype-geocode">
                        <label for="changetype-geocode">{{data.trans.label_gmap_control_geo}}</label>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Mark map location for the Venue. This will be displayed on Event page.
                </div>
            </div>



            <div class="emrow">
                <div class="emfield">{{data.trans.label_established}}</div>
                <div class="eminput">
                    <input readonly="readonly" type="text" name="established"  ng-model="data.term.established" id="established">
                    <input type="button" value="Reset" ng-click="data.term.established=''" />
                    <div class="emfield_error">
                        <span ng-show="termForm.established.$error.pattern && !termForm.established.$pristine">{{data.trans.validation_venue_date_format}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    When the Venue opened for public.
                </div>
            </div>

            <div class="emrow">
                <div class="emfield">{{data.trans.label_type}}<sup>*</sup></div>
                <div class="eminput">
                    <select required name="type"  ng-model="data.term.type" ng-options="type.key as type.label for type in data.term.types"></select>
                    <div class="emfield_error">
                        <span ng-show="termForm.type.$error.required && !termForm.type.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Type of seating arrangement- Standing or Seating.
                </div>
            </div>

            <div class="emrow" ng-show="data.term.type=='seats'">
                <div class="emfield">{{data.trans.label_seating_capacity}}<sup>*</sup></div>
                <div class="eminput">
                    <input ng-required="data.term.type=='seats'" type="number" name="seating_capacity"  ng-model="data.term.seating_capacity">
                    <div class="emfield_error">
                        <span ng-show="termForm.seating_capacity.$error.number && !termForm.seating_capacity.$pristine">{{data.trans.validation_numeric}}</span>
                        <span ng-show="termForm.seating_capacity.$error.min && !termForm.seating_capacity.$pristine">Value should be greater than 0</span>
                        <span ng-show="termForm.seating_capacity.$error.required && !termForm.seating_capacity.$pristine">{{data.trans.validation_required}}</span>
                        <span ng-show="termForm.seating_capacity.$error.invalidCapacity ">Capacity does not match with seating structure.</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Seating capacity. This should be in sync with seating area (Rows and Columns).
                </div>
            </div>

            <div class="emrow">
                <div class="emfield">Operator</div>
                <div class="eminput">
                    <input  type="text" name="seating_organizer"  ng-model="data.term.seating_organizer" ng-disabled="isSeatingDisabled">
                    <div class="emfield_error">
                        <span ng-show="termForm.seating_capacity.$error.number && !termForm.seating_capacity.$pristine">{{data.trans.validation_numeric}}</span>
                        <span ng-show="termForm.seating_capacity.$error.min && !termForm.seating_capacity.$pristine">{{data.trans.validation_numeric_min}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Venue coordinator name or contact details.
                </div>
            </div>

            <div class="emrow">
                <div class="emfield">{{data.trans.label_facebook_page}}</div>
                <div class="eminput">
                    <input class="kf-fb-field" ng-pattern="/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/?/" type="url" name="facebook_page"  ng-model="data.term.facebook_page">
                    <div class="emfield_error">
                        <span ng-show="termForm.facebook_page.$error.url && !termForm.facebook_page.$pristine">{{data.trans.validation_facebook_url}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Facebook page URL of the Venue, if available. Eg.:https://www.facebook.com/XYZ/
                </div>
            </div>

            <div class="emrow kf-bg-light">
                <div class="emfield emeditor">Venue {{data.trans.label_gallery}}</div>
                <div class="eminput emeditor">
                    <input id="media_upload" type="button" ng-click="mediaUploader(true)" class="button kf-upload" value="{{data.trans.label_upload}}" />
                    <div class="em_gallery_images">
                        <ul id="em_draggable" class="dbfl">
                            <li class="kf-db-image difl" ng-repeat="(key, value) in data.term.images" id="{{value.id}}">
                                <div><img class="difl" ng-src="{{value.src[0]}}" />
                                    <span><input class="em-remove_button" type="button" ng-click="deleteGalleryImage(value.id, key)" value="Remove"/></span> 
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="emnote emeditor">
                    Displays multiple images of the Venue as gallery.
                </div>
            </div>




            <input class="hidden" type="text" name="gallery_images" ng-model="data.term.gallery_images" />

             <div ng-show="data.term.type=='seats'">
                <div class="emrow">
                <div class="emfield">{{data.trans.label_rows}}<sup>*</sup></div>
                <div class="eminput">
                    <input id="row" ng-required="data.term.type=='seats'" ng-min='1' type="number" ng-model="rows" name="rows" />
                    <div class="emfield_error">
                        <span ng-show="termForm.rows.$error.number && !termForm.rows.$pristine">{{data.trans.validation_numeric}}</span>
                        <span ng-show="termForm.rows.$error.required && !termForm.rows.$pristine">{{data.trans.validation_required}}</span>
                        <span ng-show="termForm.rows.$error.min && !termForm.rows.$pristine">Value should be greater than 0</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    No. of rows in the seating area.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_columns}}<sup>*</sup></div>
                <div class="eminput">
                    <input id="col" ng-required="data.term.type=='seats'" ng-min='1' type="number" ng-model="columns" name="columns" />
                    <div class="emfield_error">
                        <span ng-show="termForm.columns.$error.number && !termForm.columns.$pristine">{{data.trans.validation_numeric}}</span>
                         <span ng-show="termForm.columns.$error.required && !termForm.columns.$pristine">{{data.trans.validation_required}}</span>
                        <span ng-show="termForm.columns.$error.min && !termForm.columns.$pristine">Value should be greater than 0</span>                   
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    No. of columns in the seating area.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">&nbsp;</div>
                <div class="eminput">                     
                    <div class="difr kf-seat_sequence"><input type="button" value="Create Seating Arrangement" ng-click="createSeats(rows, columns)" ng-disabled="(!rows) || (!columns)" class="kf-upload" /></div>
                </div>
                <div class="emnote">&nbsp;</div>
            </div>

            <div class="emrow em_seat_table kf-bg-light">
                <table class="em_venue_seating" ng-style="seat_container_width">

                    <tr ng-repeat="row in data.term.seats" class="row isles_row_spacer" id="row{{$index}}" ng-style="{'margin-top':row[0].rowMargin}"> 


                        <td class="row_selection_bar" ng-click="selectRow($index)">
                            <div title="Select Row">{{getRowAlphabet($index)}}</div>
                        </td>

<!--                         <div ng-if="$index==0" ng-click="selectColumn($index)">C-{{$index}}</div>-->
                        <td id="pm_seat" ng-repeat="seat in row" ng-init="adjustContainerWidth(seat)" class="seat isles_col_spacer" ng-class="seat.type" id="ui{{$parent.$index}}-{{$index}}" 
                             ng-style="{'margin-left':seat.columnMargin}">
                            <div  class="kf-col-index em_seat_col_number" ng-if="$parent.$index==0" ng-click="selectColumn($index)">{{$index}}</div>
                            <div  id="pm_seat"  class="seat_avail seat_status" ng-click="selectSeat(seat, $parent.$index, $index)" ng-click="showSeatOptions(seat)">{{seat.uniqueIndex}} </div>
                        </td>
                    </tr>
                </table>
            </div>
                
            <div class="action_bar">
                <ul>
                    <li class="difl"><input type="button" value="Add/Remove aisles" ng-click="createAisles()" ng-disabled="currentSelection != 'row' && currentSelection != 'col'"/></li>
                    <li class="difl"><input type="button" value="Reserve" ng-click="reserveSeat()"/></li>
                    <li class="difl"><input type="button" value="Reset Current Selection" ng-click="resetSelections()"/></li>
 <li class="difl"><input type="button" value="Select Scheme" ng-click="em_call_scheme_popup('#pm-change-password1')"/></li>
                </ul>
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

                <span ng-show="termForm.type.$error.required">Type</span>

                <span ng-show="termForm.seating_capacity.$error.required">Seating capacity</span>

                <span ng-show="termForm.rows.$error.required">Rows</span>

                <span ng-show="termForm.columns.$error.required">Columns</span>
                </h3>
            </div>
        </form>
         <div id="show_popup" ng-show = "IsVisible">

                            <div class="pm-popup-mask"></div>    
                            <div id="pm-change-password-dialog">
                                <div class="pm-popup-container">
                                    <div class="pm-popup-title pm-dbfl pm-bg-lt pm-pad10 pm-border-bt">
                                  
                                        
                                    

<!--                                        <div class="pm-popup-action pm-dbfl pm-pad10 pm-bg">
                                            <div class="pm-login-box GCal-confirm-message">
                                                <div class="pm-login-box-error pm-pad10" style="display:none;" id="pm_reset_passerror">
                                                </div>
                                                ---Form Starts---
                                                {{currentSeat.seatSequence}}
                                                <input type="text" id="custom_seat_seq" />
                                                <input type="button" value="Update" ng-click="updateCurrentSeat()" />
                                            </div>
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <div id="show_popup" ng-show = "scheme_popup">

                            <div class="pm-popup-mask"></div>    
                            <div id="pm-change-password1-dialog">
                                <div class="pm-popup-container">
                                    <div class="pm-popup-title pm-dbfl pm-bg-lt pm-pad10 pm-border-bt">
                                  
                                        
                                    

                                        <div class="pm-popup-action pm-dbfl pm-pad10 pm-bg">
                                            <div class="kf-popup-box GCal-confirm-message">
                                                <div class="pm-login-box-error pm-pad10" style="display:none;" id="pm_reset_passerror">
                                                </div>
                                                <!-----Form Starts----->
                                                 <div class="kf-seat_schemes dbfl">
                                                     <div class="kf-seat_schemes-titlebar">
                                                    <div class="kf-seat_schemes-title"> Scheme(s)</div>
                                                    <span  class='kf-popup-close' ng-click="scheme_popup=false">&times;</span>
                                                     </div>
                                                    <div class="emrow">
                                                        <div class="emfield"> Current scheme(s)</div>
                                                        <div class="eminput"> <div class="kf-seat_scheme difl" ng-repeat="row in selectedSeats" >
                                                    {{row.seatSequence}}
                                                    </div>
                                                        </div>
                                                    </div>
                                                       <div class="emrow">
                                                     <div class="emfield"> Change scheme(s)</div>
                                                     <div class="eminput">  <textarea id="custom_seat_sequences"></textarea></div>
                                                       </div>
                                                <div class="emrow kf-popup-button-area">
                                                    
                                                <input type="button" value="Update" ng-click="updateCurrentSeatScheme()" />
                                                </div> 
                                                </div> 
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
        
        
        

<!--        <div>
            {{currentSeat.seatSequence}}
            <input type="text" id="custom_seat_seq" />
            <input type="button" value="Update" ng-click="updateCurrentSeat()" />
        </div>-->
    </div>
</div>










