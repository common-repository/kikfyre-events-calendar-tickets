<?php 
 
    $performer = $global_options['performers_page'];
    $venue     = $global_options['venues_page'];
    $event     = $global_options['events_page'];
    $booking   = $global_options['booking_page'];
    $profile   = $global_options['profile_page'];
    $event_type= $global_options['event_types'];
      
?>
<style>
    .em-frontend {width: 98%; margin: 50px 1%;}
    .em-frontend img {max-width: 100%; height: auto;}
</style>
<div class="em-frontend">
<h1 class="wp-heading-inline">Creating 'Event Kikfyre' Front End or <i>How to create a kickass events site!</i></h1>
<b>Frontend </b><i>(n)</i>: The public facing part of your site, unlike the Dashboard which is restricted administrative area.
<hr />
<p>In <b>Event Kikfyre</b>, there are three ways to generate Frontend views.</p>
<ol>
<li><b>The Directories</b> - Events, Event Types, Venues and Performers, displayed using shortcodes.</li>
<li><b>Individual Pages</b> for Directory Items - For example a single event etc., assigned through menus.</li>
<li><b>Widgets</b> - Like Events Calendar etc., assigned from Widgets area.</li>
</ol>
<p>Here's how to display each one of them.</p>
<hr />
<h3>1. Directories <span class="dashicons dashicons-category"></span></h3>
<p>Directories are useful when you have more than one type of items. You may want your website users to browse through them and open individual items by clicking on them. Think of them as containers for individual items. Take, for example, the All Events view <i>(=container)</i> which is rendered using <code>[em_events]</code> shortcode. It displays all the Events<i>(=items)</i> you have created in <b>Event Kikfyre</b> as grid of cards. There are similar shortcodes for each type of directory. If you plan to use <b>Event Kikfyre</b> for hosting only single event, <i>you may not want to use Directory pages much, and that's perfectly fine! Our plugin was designed to work equally well in both cases.</i></p>
<p>When you first install <b>Event Kikfyre</b> these Directory pages are created automatically. Of course, you can delete them if you wish to. Restoring them is easy. You will need to create new <span class="dashicons dashicons-welcome-add-page"></span> Pages with Directory shortcodes pasted in them. Once you have done this, map these new pages inside <i>Event Kikfyre &#8594; Settings &#8594; Default Pages</i></p>
<p>So without further ado, here are the shortcodes:</p>
    <table class="wp-list-table widefat fixed striped">
    <thead>
    <tr>
        <td class="check-column"></td>
        <th class="manage-column">Shortcode</th>
        <th class="manage-column">Displays</th>
        <th class="manage-column column-title">Description</th>
        </tr>
    <tr>
        </thead>
        <td>1</td>
        <td><code>[em_events]</code></td>
        <td>All Events</td>
        <td>Events are displayed as cards, with ten cards per page. Pagination appears below cards after every 10 events. Combine this view with <b>Event Kikfyre's</b> Event Filter widget to create a full-fledged Events Directory on your site.</td>
        </tr>
    <tr>
        <td>2</td>
        <td><code>[em_event_types]</code></td>
        <td>All Event Types</td>
        <td>This will show all Event Types you created inside <b>Event Kikfyre</b>. Event Types are similar to Event Categories but with some extra properties. When your website users clicks on a Type, it will open a new page with Events only related to that Type.</td>
    </tr>
    <tr>
        <td>3</td>
        <td><code>[em_performers]</code></td>
        <td>All Performers</td>
        <td>Want to show list of all the Speakers in various conferences? Or perhaps, all the celebrities on your shows? No problem! This shortcode displayes every Performer on your site. Don't forget, each event has its own Performers tab with Event specific appearances.</td>
    </tr>
        <tr>
        <td>4</td>
        <td><code> [em_venues]</code></td>
        <td>All Venues</td>
        <td>Use this to display all venues on your site. Venue cards appear with Feature Image, Name and Short Description. Pagination appears after every 10 venues. Combine this with <b>Event Kikfyre's</b> Venue Map widget to show all venue markers on a fully stretched out map.</td>
    </tr>
    <tr>
        <td>5</td>
        <td><code> [em_profile]</code></td>
        <td>User Account Area</td>
        <td>This is not a directory <i>per se</i>. User account area is where your users can manage their bookings, print their tickets and get directions to their venues. Therefore, it is important to assign this page to an easily accessible menu item for your logged in users. Logged off users will be asked to login if they reach this page directly.</td>
    </tr>
    </table>
<br />
<hr />
<h3>2. Individual Pages <span class="dashicons dashicons-admin-page"></span></h3>
<p>Individual Items are created inside <b>Event Kikfyre</b> as special type of posts. There are 3 types of items you can create:</p>
<ol>
<li>Events <i>(Everything else is weaved around it...)</i></li>
<li>Venues</li>
<li>Performers</li>
</ol>
Let's say, you created an event named <i>My Awesome Event</i> and you want to attach it to a menu item. All you have to do is, go to <span class="dashicons dashicons-admin-appearance"></span><i>Appearance &#8594; Menus</i> On the left side, look out for <b>Kikfyre Events</b> tab. Clicking it will reveal a list of all your events with checkboxes. Click on the checkbox, and then press <i>Add to Menu</i>. Similarly, you will find <i>Venues</i> and <i>Performers</i> tabs. Let's have a quick look at how it will appear:
<img src="<?php echo plugin_dir_url(__DIR__) . 'template/images/';?>frontend-1.png">
<hr />
<h3>3. Widgets <span class="dashicons dashicons-welcome-widgets-menus"></span></h3>
<p>Any plugin worth its salt will have widgets! They provide little windows to pull relevant content on all the pages of your site. Different themes have different widget areas built inside them for this purpose. Naturally, <b>Event Kikfyre</b> comes with its own small menagerie of widgets. Widgets are found inside <span class="dashicons dashicons-admin-appearance"></span><i>Appearance &#8594; Widgets</i></p>
<h4>Event Countdown</h4>
<img src="<?php echo plugin_dir_url(__DIR__) . 'template/images/';?>frontend-3.png">
<p>Displays a reverse countdown to an upcoming event, selectable in Widget settings. Countdown is split into Days, Hours, Minutes and Seconds. A live tick tock...</p>
<h4>Events Slider</h4>
<p>An image slideshow pulling Feature Images from various Events on your site. Clicking on a picture opens the assocated Event page.</p>
<h4>Events Calendar</h4>
<img src="<?php echo plugin_dir_url(__DIR__) . 'template/images/';?>frontend-4.png">
<p>A flexible calendar with Event dates highlighted. Also, upcoming events are listed below the calendar with active links.</p>
<h4>Event Filter</h4>
<img src="<?php echo plugin_dir_url(__DIR__) . 'template/images/';?>frontend-5.png">
<p>Allow your site users to search and filter the list of Events. Offers multiple options with keyword based search, and Type and Venue filtering.</p>
<h4>Venue Map</h4>
<img src="<?php echo plugin_dir_url(__DIR__) . 'template/images/';?>frontend-6.png">
<p>A Google Map displaying different Venue markers. Make sure you have configured Google Maps API in <i>Event Kikfyre &#8594; Settings &#8594; External Integration</i> to make it work.</p>
<br />
<hr />
<h4>Keep exploring since there's a lot more to Event Kikfyre. And don't forget, we keep adding stuff with weekly releases. So keep an eye on the changelog. Also if you need help, want to request new features, just message our support team. <a href="mailto:support@kikfyre.com" target="_blank">CLICK TO EMAIL!</a></h4>
</div>