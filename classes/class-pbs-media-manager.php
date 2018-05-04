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

  public function __construct( $file ) {
    $this->file = __FILE__;
    $this->dir = dirname(__FILE__);
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->dir ) ) );
    $this->token = 'pbs_media_manager';
    $this->version = '0.1';
	}


  public function get_media_manager_client( $api_key=false, $api_secret=false, $api_endpoint=false ) {
    if (!class_exists('PBS_Media_Manager_API_Client')) {
      if (!include_once(trailingslashit($this->dir) . '../libs/class-PBS-Media-Manager-API-Client.php')) {
        return array('errors' => 'Media Manager API Client not present');
      }
    }
    $options = get_option($this->token);
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


}
