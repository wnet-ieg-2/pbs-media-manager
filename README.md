# pbs-media-manager-wordpress
PBS Media Manager for WordPress

This plugin provides the basic functionality for including the PBS Media Manager API Client into WordPress.   

## Installation
1. Copy this repository into a folder within your WordPress plugins folder
2. Enable the plugin from the WordPress plugins admin page
3. Go to the settings page for PBS Media Manager 
4. Fill in all fields on that settings page.  The Media Manager API Key and Secret you will need to request from PBS.

## Testing your installation
When all settings fields are filled in correctly, you can test the connectivity and lookup functions by using the 'Media Manager Lookup' form on the settings page.   Try selecting 'show' on that form and entering the show slug for a show you know you have access to, such as 'poldark' or 'newshour', and clicking 'submit'.  The raw JSON will appear below the form, either with pretty complete information about the show or with some error messaging from PBS.  You can try other shows, individual videos without reloading the page.  

NOTE: TP Media IDs are NOT, by default, returned in the data from the Media Manager API.  This is set on a per-keypair basis.  If TP Media ID's aren't appearing in your asset results, or if you want to do the lookup by TP Media ID, you'll need to submit a ticket to PBS via the PBS Digital Support portal; "Please enable TP Media IDs for my Media Manager API Keys" should do it.   PBS may at some point phase out the TP Media ID.

## Using the client in your themes or functions
Once you've enabled the plugin, the PBS_Media_Manager class will be available to program with.  It has a single method (at this point), 'get_media_manager_client()'.   This will create a new instance of the PBS_Media_Manager_API_Client https://github.com/tamw-wnet/PBS_Media_Manager_Client using the API keys etc you've added in the settings page for this plugin.  

Once the client has been created, all methods detailed in that repository are available -- for instance, here's how to get the show-level information about the PBS NewsHour; 
```php
$pluginobj =  new PBS_Media_Manager(__FILE__);
$client = $pluginobj->get_media_manager_client();
$show_slug = 'newshour';
$showdata = $client->get_item_of_type($show_slug, 'show');
```
$showdata will be an object containing all the top-level information about the show.



## Changelog
* Version .01 ALPHA -- provides basic wrapping and testing function for the PBS Media Manager API. 

## Authors
* William Tam, WNET/IEG

## Licence
PBS Media Manager for WordPress is licensed under the GPL v2 or later.

> This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

> This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

> You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
