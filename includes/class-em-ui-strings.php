<?php

/**
 * This class works as a repository of all the string resources used in  UI
 * for easy translation and management. 
 *
 */
class EventM_UI_Strings {

    public static function get($identifier) {
        $identifier = strtoupper($identifier);
        switch ($identifier) {
            case 'LABEL_PAYMENTS':
                return __("Payments",'event-magic');
            case 'LABEL_PAYMENTS_SUB':
                return __("Payment gateway configuration settings",'event-magic');    
            case 'LABEL_NAME':
                return __('Name', 'event-magic');

            case 'LABEL_ADDRESS':
                return __('Venue Location', 'event-magic');

            case 'HEADING_VENUE_PAGE':
                return __('Add/Edit Venue', 'event-magic');

            case 'VALIDATION_NUMERIC':
                return __('Only numeric value allowed.', 'event-magic');

            case 'VALIDATION_REQUIRED':
                return __('This is a required field.', 'event-magic');

            case 'VALIDATION_NUMERIC_MIN':
                return __('Minimum allowed value is %d.', 'event-magic');

            case 'VALIDATION_DISCOUNT_PER_MAX':
                return __('Can not be more than 100%', 'event-magic');

            case 'VALIDATION_VENUE_MIN_LENGTH':
                return __('At least %d characters required.', 'event-magic');

            case 'VALIDATION_EMAIL':
                return __('Invalid Email', 'event-magic');
                
            case 'VALIDATION_PUBLISHABLE_KEY':
                return __('Publishable Key cannot be left blank', 'event-magic');
                
            case 'VALIDATION_SECRET_KEY':
                return __('Secret Key cannot be left blank', 'event-magic');

            case 'VALIDATION_ANET_LOGIN_KEY':
                return __('Login Key cannot be left blank','event-magic');
            
            case 'VALIDATION_ANET_TRANS_KEY':
                return __('Transaction Key cannot be left blank','event-magic');
                
            case 'VALIDATION_ANET_CLIENT_KEY':
                return __('Client Key cannot be left blank','event-magic');                
                
            case 'LABEL_GMAP_CONTROL_ALL':
                return __('All', 'event-magic');

            case 'LABEL_GMAP_CONTROL_EST':
                return __('Establishments', 'event-magic');

            case 'LABEL_GMAP_CONTROL_GEO':
                return __('Geocodes', 'event-magic');

            case 'LABEL_SAVE_VENUE':
                return __('Save Venue', 'event-magic');

            case 'LABEL_ESTABLISHED':
                return __('Established', 'event-magic');

            case 'VALIDATION_DATE_FORMAT':
                return __('Format should be DD/MM/YYYY', 'event-magic');

            case 'LABEL_SEATING_ORGANIZER':
                return __('Seating Organizer', 'event-magic');

            case 'VALIDATION_VENUE_MAX_LENGTH':
                return __('Input exceeds maximun character limits.', 'event-magic');

            case 'LABEL_VENUE_GALLERY':
                return __('Upload Gallery', 'event-magic');

            case 'LABEL_SLUG':
                return __('Slug', 'event-magic');

            case 'LABEL_VENUE_FACEBOOK_PAGE':
                return __('Facebook Page', 'event-magic');

            case 'VALIDATION_INVALID_URL':
                return __('Please use a valid URL', 'event-magic');

            case 'LABEL_SAVE':
                return __('Save', 'event-magic');

            case 'LABEL_VENUE':
                return __('Venue', 'event-magic');

            case 'LABEL_COVER_IMAGE':
                return __('Cover Image', 'event-magic');

            case 'LABEL_GALLERY':
                return __('Gallery', 'event-magic');

            case 'LABEL_PERFORMERS':
                return __('Performers', 'event-magic');

            case 'LABEL_PERFORMER_ROLE':
                return __('Performer Role', 'event-magic');

            case 'LABEL_PERFORMER_NAME':
                return __('Performer Name', 'event-magic');

            case 'LABEL_ADD_PERFORMER':
                return __('Add Performer', 'event-magic');

            case 'LABEL_PERFORMER_TYPE':
                return __('Performer Type', 'event-magic');

            case 'LABEL_PERSON':
                return __('Person', 'event-magic');

            case 'LABEL_GROUP':
                return __('Group', 'event-magic');

            case 'LABEL_ROLE':
                return __('Role', 'event-magic');

            case 'LABEL_IMAGE':
                return __('Image', 'event-magic');

            case 'LABEL_PERFORMER_DISPLAY':
                return __('Display on list of performers', 'event-magic');

            case 'HEADING_NEW_EVENT_TYPE':
                return __('Add/Edit Event Type', 'event-magic');

            case 'LABEL_COLOR':
                return __('Color', 'event-magic');

            case 'LABEL_AGE_GROUP':
                return __('Age Group', 'event-magic');

            case 'LABEL_SPECIAL_INSTRUCTIONS':
                return __('Special Instructions', 'event-magic');

            case 'LABEL_ALL':
                return __('All', 'event-magic');

            case 'LABEL_ALL_PARENTAL_GUIDANCE':
                return __('All ages but parental guidance', 'event-magic');

            case 'LABEL_CUSTOM_AGE':
                return __('Custom Age', 'event-magic');

            case 'LABEL_ADD_TICKET':
                return __('Add Ticket', 'event-magic');

            case 'LABEL_BORDER_COLOR':
                return __('Border Color', 'event-magic');

            case 'LABEL_BACKGROUND_COLOR':
                return __('Background Color', 'event-magic');

            case 'HEADING_NEW_TICKET':
                return __('Add/Edit Ticket Template', 'event-magic');

            case 'LABEL_LOGO':
                return __('Logo', 'event-magic');

            case 'LABEL_BACKGROUND_PATTERN':
                return __('First', 'event-magic');

            case 'LABEL_FONT1':
                return __('Font1', 'event-magic');

            case 'LABEL_FONT2':
                return __('Font2', 'event-magic');

            case 'LABEL_CHOOSE_TEMPLATE':
                return __('Choose Template', 'event-magic');

            case 'LABEL_NEW_PERFORMER':
                return __('Add New Performer(s)', 'event-magic');

            case 'LABEL_START_DATE':
                return __('Day Starts', 'event-magic');


            case 'LABEL_RECURRENCE':
                return __('Recurrence', 'event-magic');


            case 'VALIDATION_DATE_FORMAT':
                return __('Format should be DD/MM/YYYY', 'event-magic');

            case 'LABEL_END_DATE':
                return __('Day Ends', 'event-magic');

            case 'LABEL_RECURRING':
                return __('Recurring', 'event-magic');

            case 'LABEL_SPECIFIC_DATES':
                return __('Specific Dates', 'event-magic');

            case 'LABEL_PERFORMER':
                return __('Performer', 'event-magic');

            case 'LABEL_RECURRENCE_DATE':
                return __('Recurrence Date', 'event-magic');

            case 'LABEL_RECURRENCE_INTERVAL':
                return __('Recurrence Interval', 'event-magic');

            case 'LABEL_RECURRENCE_DATES':
                return __('Recurrence Dates', 'event-magic');

            case 'LABEL_SEATS':
                return __('Seats', 'event-magic');

            case 'LABEL_STANDING':
                return __('Standing', 'event-magic');

            case 'LABEL_ORGANIZER_NAME':
                return __('Organized by', 'event-magic');

            case 'LABEL_ORGANIZER_CONTACT_DETAILS':
                return __('Organizer Contact Details', 'event-magic');

            case 'LABEL_HIDE_EVENT_FROM_CALENDAR':
                return __('Hide on Events Calendar Widget', 'event-magic');

            case 'LABEL_HIDE_EVENT_FROM_EVENTS':
                return __('Hide from Events Directory', 'event-magic');

            case 'LABEL_TICKET_PRICE':
                return __('Ticket Price', 'event-magic');
                
             case 'LABEL_OVERRIDE_TICKET_PRICE':
                return __('Override Ticket Price', 'event-magic');
                 
            case 'LABEL_EDIT_SEATING_ARRANGEMENT':
                return __('Edit Seating Arrangement ', 'event-magic');

            case 'LABEL_TICKET_MANAGER':
                return __('Ticket Manager', 'event-magic');

            case 'LABEL_MAX_TICKETS_PER_PERSON':
                return __('Max Tickets Per Person', 'event-magic');

            case 'LABEL_ALLOW_CANCELLATIONS':
                return __('Allow Cancellations', 'event-magic');

            case 'LABEL_AUDIENCE_NOTICE':
                return __('Note For Audience', 'event-magic');

            case 'LABEL_ALLOW_VOLUME_DISCOUNT':
                return __("Allow Volume Discount", 'event-magic');

            case 'LABEL_DISCOUNT_NO_TICKETS':
                return __("Minimum Number of Tickets", 'event-magic');

            case 'LABEL_DISCOUNT_PER':
                return __("Discount Percentage(%)", 'event-magic');

            case 'LABEL_FACEBOOK_PAGE':
                return __("Facebook Page", 'event-magic');

            case 'LABEL_SELECT_EVENT':
                return __("Select an Event", 'event-magic');

            case 'LABEL_SELECT_PERFORMER':
                return __("Please select Performer", 'event-magic');

            case 'LABEL_SELECT_TICKET_TEMPLATE':
                return __("Select a ticket template", 'event-magic');

            case 'LABEL_SELECT_RECURRENCE':
                return __('Select Recurrence', 'event-magic');

            case 'LABEL_SELECT_VENUE':
                return __('Select Venue', 'event-magic');

            case 'LABEL_HIDE_ORGANIZER':
                return __('Hide Organizer Details', 'event-magic');

            case 'LABEL_SPONSER_IMAGE':
                return __('Sponsor Logos', 'event-magic');

            case 'VALIDATION_INVALID_DATE_VALUE':
                return __('Invalid Date', 'event-magic');

            case 'LABEL_STATUS':
                return __('Status', 'event-magic');

            case 'LABEL_ADD_NEW':
                return __('Add New', 'event-magic');

            case 'LABEL_DELETE':
                return __('Delete', 'event-magic');

            case 'LABEL_HIDE_EXPIRED':
                return __('Hide Past Events', 'event-magic');
                
            case 'LABEL_DUPLICATE':
                return __('Duplicate', 'event-magic');

            case 'LABEL_SORT_BY':
                return __('Sort By', 'event-magic');

            case 'HEADING_NEW_EVENT_PAGE':
                return __('Add/Edit Event', 'event-magic');

            case 'LABEL_TYPE':
                return __('Seating Type', 'event-magic');

            case 'LABEL_SELECT':
                return __('Please Select', 'event-magic');

            case 'LABEL_DESCRIPTION':
                return __('Description', 'event-magic');

            case 'LABEL_UPLOAD':
                return __('Upload', 'event-magic');

            case 'HEADING_EVENTS_MANAGER':
                return __('Events Manager', 'event-magic');

            case 'LABEL_AT':
                return __('at ', 'event-magic');

            case 'LABEL_ON':
                return __('on', 'event-magic');

            case 'HEADING_VENUE_MANAGER':
                return __('Venue Manager', 'event-magic');

            case 'LABEL_ROWS':
                return __('Rows', 'event-magic');

            case 'LABEL_ADD_EVENT_TYPE':
                return __('Add New Event Type', 'event-magic');

            case 'LABEL_COLUMNS':
                return __('Columns', 'event-magic');

            case 'LABEL_CANCEL':
                return __('Cancel', 'event-magic');

            case 'LABEL_SEATING_CAPACITY':
                return __('Seating Capacity', 'event-magic');

            case 'LABEL_HEADING_EVENT_TYPE_MANAGER':
                return __('Event Types', 'event-magic');

            case 'HEADING_NEW_PERFORMER':
                return __('Add/Edit Performer', 'event-magic');

            case 'HEADING_TICKET_MANAGER':
                return __('Ticket Manager', 'event-magic');

            case 'LABEL_TICKET_MANAGERS':
                return __('Ticket Managers', 'event-magic');

            case 'LABEL_GLOBAL_SETTINGS':
                return __('Settings', 'event-magic');

            case 'HEADING_NEW_TICKET_TEMPLATE':
                return __('Add/Edit Ticket Template', 'event-magic');

            case 'LABEL_NO_BOOKING_RECORDS':
                return __('No Booking Matches your Criteria', 'event-magic');

            case 'LABEL_NO_RECORDS':
                return __('No Record Found', 'event-magic');

            case 'LABEL_BOOKING_ID':
                return __('BOOKING ID', 'event-magic');

            case 'LABEL_BOOKING_STATUS':
                return __('Status', 'event-magic');

            case 'VALIDATION_FACEBOOK_URL':
                return __('Invalid Facebook URL', 'event-magic');

            case 'VALIDATION_INVALID_END_DATE':
                return __('End date should be greater than start date');

            case 'LABEL_ADD_NEW_VENUE':
                return __('Add New Venue');

            case 'LABEL_GOOGLE_MAP_API_KEY':
                return __('Google Map API Key');

            case 'LABEL_FACEBOOK_API_KEY':
                return __('Facebook API Key');

            case 'LABEL_GOOGLE_CAL_API_KEY':
                return __('Google Calendar API Key');

            case 'LABEL_GOOGLE_CAL_CLIENT_ID':
                return __('Google Calendar Client ID');

            case 'HEADING_GLOBAL_SETTINGS':
                return __('Global Settings');

            case 'NOTICE_VENUE_MAP_NOT_CONFIGURED':
                return __("Location field is not active as Google Map API not configured. You can confiure it from Global Settings");

            case 'NOTE_EVENT_NAME':
                return __('Event Note Name');

            case 'LABEL_BOOKINGS':
                return __('Bookings');

            case 'LABEL_BOOKING':
                return __('Booking');

            case 'LABEL_ANALYTICS':
                return __('Analytics');

            case 'LABEL_TICKET_TEMPLATE':
                return __('Ticket Template');

            case 'LABEL_LAST_BOOKING_DATE':
                return __('Last booking date');
                
            case 'LABEL_START_BOOKING_DATE':
                return __('Start booking date');
                
            case 'VALIDATION_INVALID_LAST_BOOKING_DATE':
                return __('Last booking date should be less than End date.');

            case 'LABEL_UPCOMING_EVENTS' :
                return __('Upcoming Events');

            case 'LABE_DESCRIPTION':
                return __('Description');

            case 'LABEL_REGISTER_NOW':
                return __('Register Now');

            case 'LABEL_REGISTER_NOW':
                return __('Register Now');

            case 'LABEL_NU_EVENTS':
                return __('No. of events');

            case 'LABEL_ALPHABETICALLY':
                return __('Alphabetically');

            case 'LABEL_DATE':
                return __('Date');
            case 'LABEL_DATE_TIME':
                return __('Date and Time');

            case 'LABEL_TEST_MODE':
                return __('Test Mode');

            case 'LABEL_PAYMENT_PROCESSOR':
                return __('Payment Processor');

            case 'LABEL_PAYPAL_EMAIL':
                return __('Paypal Email');

            case 'LABEL_PAYPAL_PAGE_STYLE':
                return __('Paypal Page Style');

            case 'LABEL_EVENTS_HERE':
                return __('Events Here');

            case 'LABEL_SEATING_CAPACITY':
                return __('Seating Capacity');

            case 'LABEL_ADDRESS':
                return __('Address');

            case 'UPCOMING_EVENTS':
                return __('UPCOMING EVENTS');
            
            case 'LABEL_UPCOMING_EVENTS_HERE':
                return __('Upcoming Events Here', 'event-magic');

            case 'LABEL_EVENT_MAGIC_CALENDAR':
                return __("KikFyre Calendar");

            case 'LABEL_CALENDAR_WIDGET_DESC':
                return __("Event Calendar to show all the events");

            case 'LABEL_VENUE_MAP':
                return __("KikFyre - Venues Map");

            case 'LABEL_VENUE_WIDGET_DESC':
                return __("Map to show all the venue locations");

            /* case 'LABEL_ORGANIZER_NAME':
              return __("Organizer Name"); */

            case 'LABEL_ORGANIZER_PHONE':
                return __("Organizer Phone");

            case 'LABEL_SPECIAL_INSTRUCTIONS':
                return __("Special Instructions");

            case 'LABEL_AGE_GROUP':
                return __("Age Group");

            case 'LABEL_EVENT_MAGIC_SLIDER':
                return __("KikFyre - Event Slider");

            case 'LABEL_SLIDER_WIDGET_DESC':
                return __("Event Slider to show all the events");

            case 'LABEL_WHERE':
                return __("Where");

            case 'LABEL_EVENT_MAGIC_VENUE_FILTER':
                return __("KikFyre - Event Filter");

            case 'LABEL_VENUE_FILTER_WIDGET_DESC';
                return __("Filter Event based on selection");

            case 'LABEL_TO';
                return __("to");

            /* case 'LABEL_ORGANIZER_DETAILS';
              return __("ORGANIZER DETAILS"); */

            case 'LABEL_SPECIAL_INSTRUCTIONS';
                return __("SPECIAL INSTRUCTIONS");
            case 'LABEL_REGISTRATION_EMAIL_CONTENT':
                return __('Registration Email Body', 'event-magic');

            case 'LABEL_REGISTRATION_EMAIL_SUBJECT':
                return __('Registration Email Subject   ', 'event-magic');

            case 'VALIDATION_USER_NOT_EXISTS':
                return __('User does not exists. Please check your details.', 'event-magic');

            case 'VALIDATION_USER_NOT_ACTIVE':
                return __('User is not active yet.', 'event-magic');

            case 'VALIDATION_INVALID_LOGIN':
                return __('Wrong Username/Email or Password.', 'event-magic');

            case 'LABEL_BOOK_NOW':
                return __('Book Now', 'event-magic');

            case 'HEADING_EVENT_TYPES':
                return __('Event Types Manager', 'event-magic');

            case 'LABEL_MARK_ALL':
                return __('Mark All', 'event-magic');

            case 'ERROR_SEAT_CONFLICT':
                return __('Something went wrong. Please try again.', 'event-magic');

            case 'LABEL_PAYPAL':
                return __('Paypal', 'event-magic');

            case 'LABEL_EXPORT_ALL':
                return __("Export All", 'event-magic');

            case 'LABEL_TODAY':
                return __("Today", 'event-magic');

            case 'LABEL_THIS_WEEK':
                return __("This Week", 'event-magic');

            case 'LABEL_THIS_YEAR':
                return __("This Year", 'event-magic');

            case 'LABEL_THIS_MONTH':
                return __("This Month", 'event-magic');

            case 'LABEL_TIME':
                return __("Time", 'event-magic');

            case 'LABEL_EMAIL':
                return __("Email", 'event-magic');

            case 'LABEL_ACTIONS':
                return __("Actions", 'event-magic');

            case 'LABEL_USERNAME':
                return __("USER", 'event-magic');

            case 'LABEL_NO_TICKETS':
                return __("No. Of Tickets", 'event-magic');

            case 'HEADING_BOOKING_MANAGER':
                return __('Booking Manager', 'event-magic');

            case 'LABEL_DISPLAYING_FOR':
                return __('Displaying For', 'event-magic');

            case 'LABEL_SPECIFIC_PERIOD':
                return __("Specific Period", 'event-magic');

            case 'LABEL_FROM':
                return __("From", 'event-magic');

            case 'LABEL_TO':
                return __("To", 'event-magic');

            case 'LABEL_SEARCH':
                return __("Search", 'event-magic');

            case 'LABEL_FILTER':
                return __("Filter", 'event-magic');

            case 'LABEL_BOOKING_PENDING_CONTENT':
                return __("Booking Pending Email", 'event-magic');

            case 'LABEL_BOOKING_CONFIRMED_CONTENT':
                return __("Booking Confirmation Email", 'event-magic');

            case 'LABEL_BOOKING_CANCELATION_CONTENT':
                return __("Booking Cancellation Email", 'event-magic');

            case 'LABEL_BOOKING_REFUND_CONTENT':
                return __("Booking Refund Email", 'event-magic');


            case 'LABEL_VIEW':
                return __("View", 'event-magic');

            case 'ERROR_CAPACITY':
                return __("Booking can't be done as no seats are available.", 'event-magic');

            case 'LABEL_PAYPAL_API_USERNAME':
                return __("Paypal API Username", 'event-magic');

            case 'LABEL_PAYPAL_API_PASSWORD':
                return __("Paypal API Password", 'event-magic');

            case 'LABEL_PAYPAL_API_SIG':
                return __("Paypal API Signature", 'event-magic');

            case 'MSG_REFUND_ERROR':
                return __("Something went wrong. Refund transaction was  unsuccessful. Please try from merchant interface.", 'event-magic');

            case 'MSG_REFUND_SUCCESS':
                return __("Refund process completed.", 'event-magic');

            case 'EVENT_NAME':
                return __("EVENT NAME", 'event-magic');

            case 'EVENT_DATE':
                return __("EVENT DATE", 'event-magic');

            case 'WELCOME':
                return __("Welcome", 'event-magic');

            case 'MY_BOOKINGS':
                return __("MY BOOKINGS", 'event-magic');

            case 'DIRECTIONS':
                return __("DIRECTIONS", 'event-magic');

            case 'TRANSACTIONS':
                return __("TRANSACTIONS", 'event-magic');

            case 'ACCOUNT':
                return __("ACCOUNT", 'event-magic');

            case 'LABEL_CAPACITY':
                return __("Capacity", 'event-magic');

            case 'LABEL_AMOUNT':
                return __("AMOUNT", 'event-magic');

            case 'LABEL_ACTION':
                return __("ACTION", 'event-magic');

            case 'VALIDATION_VENUE_CAPACITY_EXCEEDED':
                return __("Capacity exceeded more than the Venue capacity", 'event-magic');

            case 'LABEL_TOUR':
                return __('Tour', 'event-magic');
            case 'LABEL_AVAILABLE': return __("Available", 'event-magic');
            case 'LABEL_BOOKED': return __("Booked", 'event-magic');
            case 'LABEL_RESERVED': return __("Reserved", 'event-magic');
            case 'LABEL_SELECTED': return __("Selected", 'event-magic');
            case 'NOTICE_MAX_TICKET_PP':
                return __("Note : Maximum seats allowed per booking - ", 'event-magic');
            case 'LABEL_SOLD_OUT':
                return __("Sold Out", 'event-magic');
            case 'LABEL_SOLD':
                return __("Sold", 'event-magic');    
            case 'LABEL_PER_SEAT':
                return __("Per Seat", 'event-magic');
            case 'LABEL_EXPIRED':
                return __("Expired", 'event-magic');
            case 'LABEL_ADD_CART':
                return __("Add To Cart", 'event-magic');
            case 'LABEL_SHOW_CART':
                return __("Show Cart", 'event-magic');
            case 'LABEL_ALL_EYE':
                return __("ALL EYES THIS WAY", 'event-magic');
            case 'LABEL_BOOKING_SUMMARY':
                return __("Your Booking Summary", 'event-magic');
                
            case 'LABEL_EVENT_BOOKING_STATUS':
                return __("Booking Status", 'event-magic');
                
            case 'LABEL_EVENT_BOOKING_EXPIRED':
                return __("Booking Expired", 'event-magic');
                
            case 'LABEL_REGISTER_NOW':
                return __("Register Now", 'event-magic');
                
            case 'LABEL_EVENT_END':
                return __("This Event has Ended", 'event-magic');
                
             case 'LABEL_BOOKING_NOT_STARTED':
                return __("Booking is not started", 'event-magic');
                
            case 'LABEL_DISCOUNT':
                return __("Discount", 'event-magic');
            case 'LABEL_SUBTOTAL':
                return __("Subtotal", 'event-magic');
                
            case 'LABEL_TOTAL_DUE':
                return __("Total Due", 'event-magic');
            case 'LABEL_PROCEED':
                return __("Checkout", 'event-magic');
            case 'NOTICE_CHECKOUT_TIMER':
                return __("Your seats are on hold. You will need to checkout within <a><span id='em_payment_timer'></span></a> minutes to reserve them. Otherwise they will be released for booking.", 'event-magic');
              case 'NOTICE_CHECKOUT_PRINT-TICKETS':
                return __("Note: You will be able to print your tickets after the checkout.", 'event-magic');
           case 'LABEL_UPDATE_CART':
                return __("Update Cart", 'event-magic');
            case 'LABEL_DETAILS':
                return __("Details",'event-magic');   
            case 'LABEL_STANDINGS':
                return __("Standings",'event-magic');   
            case 'NOTICE_NO_UPCOMING_EVENTS':
                return __("No upcoming events found.",'event-magic'); 
            case 'LABEL_MATCH':
                return __("Match",'event-magic');   
            case 'NOTE_PERFORMER_MATCH':
                return __("Please select two performers.",'event-magic'); 
            case 'LABEL_EVENT_DURATION':
                return __("Event Duration",'event-magic');    
            case 'LABEL_SELECT_DURATION':
                return __("Select Duration",'event-magic');     
            case 'LABEL_SINGLE_DAY':
                return __("Single Day",'event-magic'); 
            case 'LABEL_MULTI_DAYS':
                return __("Multiple Days",'event-magic');    
            case 'LABEL_CLOSE':
                return __("Close",'event-magic');    
            case 'HEADING_CHILD_EVENT_PAGE':
                return __("Edit Day Event",'event-magic');  
            case 'LABEL_DAILY_SCHEDULE':
                return __("Enable Daily Event Schedule",'event-magic');   
            case 'LABEL_EMAIL_NOTIFICATIONS':
                return __("Email Notifications",'event-magic'); 
            case 'LABEL_GENERAL_SETTINGS':
                return __("Regular Settings",'event-magic');  
            case 'LABEL_DEFAULT_PAGES':
                return __("Default Pages",'event-magic');   
            case 'NOTE_SHORTCODE_PAGE':
                return __("Pages with shortcodes",'event-magic'); 
            case 'NOTE_EXINT_PAGE':
                return __("Map Integration, Social Sharing...",'event-magic'); 
             case 'LABEL_EX_INT':
                return __("External Integration",'event-magic');
            case 'LABEL_FREE':
                return __("Free",'event-magic');
            Case 'LABEL_BOOKING_STARTS':
                return __("Booking Starts",'event-magic');
                Case 'LABEL_BOOKING_ENDS':
                return __("Booking Ends",'event-magic');
            case 'LABEL_BOOKING_CLOSED':
                return __("Booking Closed",'event-magic');
            case 'LABEL_BOOKING_EXPIRED':
                return __("Booking Expired",'event-magic');
            case 'LABEL_BOOKING_STATUS':
                return __("Booking Status",'event-magic');
            case 'LABEL_SOLD':
                return __("Sold",'event-magic'); 
            case 'LABEL_EVENT_ENDED':
                return __("THIS EVENT HAS ENDED",'event-magic');
            case 'LABEL_ADD_TO_CALENDAR':
                return __("Add To Calendar",'event-magic');
            case 'LABEL_EVENT_ADDED':
                return __("Event Added",'event-magic');
            case 'LABEL_LOCATION':
                return __("Location",'event-magic');
            case 'LABEL_VENUE_DETAILS_NOT_AVAILABLE':
                return __("Venue details are not available.",'event-magic');
            case 'LABEL_CAN_HOLD':
                return __("Can hold",'event-magic'); 
            Case 'LABEL_PEOPLE':
                return __("people",'event-magic');
            case 'LABEL_NOTE':
                return __('Note', 'event-magic');
                
            case 'LABEL_ORGANIZER':
                return __('Organizer', 'event-magic');
            case 'LABEL_FEATURING':
                return __('FEATURING', 'event-magic');
            case 'LABEL_EVENT_DETAILS':
                return __('Event Details', 'event-magic');
            case 'LABEL_EVENT_PHOTOS':
                return __('Event Photos', 'event-magic');
                
            case 'LABEL_LIVE':
                return __('Live', 'event-magic');
                
            case 'LABEL_NO_UPCOMING_EVENT_AT_VENUE':
                return __('No upcoming events at this venue.', 'event-magic');
            
            case 'LABEL_SPONSOR':
                return __('Sponsors', 'event-magic');
                
            default:
                return __('NO STRING FOUND', 'event-magic');
        }
    }

}
