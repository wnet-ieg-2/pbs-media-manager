<?php
/* PBS_Media_Manager 
 * This class should be loaded on every plugin load
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class PBS_Media_Manager {
  private $dir;
  private $file;
  public $assets_url;
  public $token;
  public $version;
  public $options;

  public function __construct( $file ) {
    $this->file = __FILE__;
    $this->dir = dirname(__FILE__);
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->dir ) ) );
    $this->token = 'pbs_media_manager';
    $this->version = '0.1';
    $this->options = get_option($this->token);
	}


  public function get_media_manager_client( $api_key=false, $api_secret=false, $api_endpoint=false ) {
    if (!class_exists('PBS_Media_Manager_API_Client')) {
      if (!include_once(trailingslashit($this->dir) . '../libs/class-PBS-Media-Manager-API-Client.php')) {
        return array('errors' => 'Media Manager API Client not present');
      }
    }
    $options = $this->options;
    $client_key = !empty($options['mm_api_id']) ? $options['mm_api_id'] : false;
    $client_secret = !empty($options['mm_api_secret']) ? $options['mm_api_secret'] : false;
    $client_endpoint = !empty($options['mm_api_base']) ? $options['mm_api_base'] : false;
    if ($api_key && $api_secret && $api_endpoint) {
      $client_key = $api_key;
      $client_secret = $api_secret;
      $client_endpoint = $api_endpoint;
    }
    if (!$client_key || !$client_secret || !$client_endpoint) {
      return array('errors' => 'Missing key, secret, or endpoint');
    }
    $client = new PBS_Media_Manager_API_Client($client_key, $client_secret, $client_endpoint);
    return $client;
  }

  public function derive_asset_availability_from_asset_data($asset) {
    /* this helper function returns the current availability for an asset from the record,
     * including "not_available" if there's some sort of error, and 
     * optionally an "expiration_date" value if there's a future expiration date for that window
     * and optionally an "error" which will be the error code if it was some sort of error */
    $window = 'not_available';
    $expire_date = null;
    $current_timestamp = strtotime("now");
    $next_available_date = false;
    $next_available_date_ts = 0;
    if (!empty($asset['errors'])) {
      // get the error code and return that
      $status = !empty($asset['errors']['info']['http_code']) ? $asset['errors']['info']['http_code'] : 'unknown_error';
      return array('window' => $window, 'error' => $status);
    }
    // check for passport availability stuff
    $attribs = $asset['data']['attributes'];
    if (!empty($attribs['availability_window'])) {
      // easier:  if a station uid was passed will we get this value, it will reflect the current availability
      $window = $attribs['availability_window'];
      $expire_date = $array['availabilities'][$window]['end']; // will either be null or a date string in the future
    } else {
      // go through from most restrictive to least 
      $windows = array('station_members', 'all_members', 'public');
      foreach ($windows as $this_window) {
        if (empty( $attribs['availabilities'][$this_window]['start'])) {
          continue;
        }
        $this_available_date = $attribs['availabilities'][$this_window]['start'];
        $this_available_ts = strtotime($this_available_date);
        if ($this_available_ts < $current_timestamp) {
          // the window started in the past, but may not have expired yet
          if (empty( $attribs['availabilities'][$this_window]['end']) || (strtotime($attribs['availabilities'][$this_window]['end']) > $current_timestamp ) ) {
            // expiration date is either unset or in the future, we are in this window now!
            $window = $this_window;
            $expire_date = $attribs['availabilities'][$this_window]['end']; // will either be null or a date string in the future
          }
          // the else case for above is that the window expired in the past, so we don't care about it
        } else {
          // this availability starts now or in the future
          if (!$next_available_date_ts) {
            // not set, lets set it
            $next_available_date_ts = $this_available_ts;
          }
          if ($this_available_ts <= $next_available_date_ts) {
            // happening sooner than what is on record
            // gt or eq because this one is more permissive
            $next_available_date_ts = $this_available_ts;
            $next_available_date = $this_available_date;
          }
        }
      }
    }
    $response = array('window' => $window, 'expiration_date' => $expire_date );
    if ($window == 'not_available' && $next_available_date ) {
      $response['future_available_date'] = $next_available_date;
    }
    return $response;
  }

}
