<?php 
if (!function_exists('build_media_manager_video_player')) {
function build_media_manager_video_player($asset) {
  /* this function returns the HTML for a player based on the retrieved asset record 
   * and it will show an overlay and login for the video if the video is within a member-only window
   * You could invoke it like so -- 
   * $content_id = 'less-author-andrew-sean-greer-answers-your-questions-1530132457'; //some media manager content id or slug, this example is a Newshour clip's slug
   * $plugin = new PBS_Media_Manager(); // requires the plugin to be active of course -- once created it can be reused
   * $client = $plugin->get_media_manager_client(); // also can be reused once created
   * $raw_asset = $client->get_asset($content_id, array('platform-slug' => 'partnerplayer')); // the platform-slug argument because we're building a partner player
   * $player = build_media_manager_video_player($asset['data']); // what will be returned will be either html or an empty string
   * echo $player;
 */

  // for now we build the player with the tp media id.  Media Manager API key has to have this enabled, request that from PBS
  if (empty($asset['legacy_tp_media_id'])) {
    return;
  }
  $tp_media_id = $asset['legacy_tp_media_id'];

  $plugin = new PBS_Media_Manager();

  // get the availability of the video
  $availability = $plugin->derive_asset_availability_from_asset_data($asset);
  $window = $availability['window']; 
  if ($window != 'public') {
    if ($plugin->options['enable_passport'] != 'true') {
      // only display public videos if passport not enabled
      return;
    }
    if (!in_array($window, array('all_members', 'station_members'))) {
      // passport is enabled, but this video is expired or has some other problem
      return;
    }
  }
 
  $baseplayer = '<div class="passportcoveplayer" data-window="public" data-media="' . $tp_media_id . '"><div class="embed-container video-wrap"><iframe id="partnerPlayer" marginwidth="0" marginheight="0" scrolling="no" src="//player.pbs.org/widget/partnerplayer/' . $tp_media_id . '/?chapterbar=false" allow="encrypted-media" allowfullscreen="" frameborder="0"></iframe></div></div>';

 
  // Display a "public" video as is
  if ($window == 'public') {
    return $baseplayer;
  }

  // things get more complicated with a passport video.
  // the PBS Passport Authenticate plugin is required for custom overlay 
  if (!class_exists('PBS_Passport_Authenticate')) {
    // return the regular player, PBS will display their gating
    return $baseplayer;
  }

  // PBS Passport Authenticate exists, show that prettier gating
  $passport_defaults = get_option('pbs_passport_authenticate');
	$join_url = !empty($passport_defaults['join_url']) ? $passport_defaults['join_url'] : '#';
  $station_passport_logo_reverse = !empty($passport_defaults['station_passport_logo_reverse']) ? $passport_defaults['station_passport_logo_reverse'] : $passport_defaults['station_passport_logo'];
  $station_nice_name = !empty($passport_defaults['station_nice_name']) ? $passport_defaults['station_nice_name'] : "";

  // get the mezz image
  $imgDir = get_bloginfo('template_directory');
  $large_thumb = $imgDir . "/libs/images/default.png";
  if (!empty($asset['attributes']['images'][0]['image'])) {
    $large_thumb = $asset['attributes']['images'][0]['image'] . 'resize.1200x675.jpg';
  }

  $passportOverlay = "
			<div class='signup'><div class='signup-inner'>
				<div class='pp-intro'>
					<p>Access to this video is a<br/> benefit for members through</p>
					<img src='$station_passport_logo_reverse' alt='$station_nice_name Passport'/>
				</div>
				<div class='pp-button pbs_passport_authenticate cf'><button class='launch'>
					MEMBER SIGN IN <span class='icon-passport-compass'><i></i></span>
				</button></div>
				<div class='pp-button pbs_passport_authenticate'>
					<a href='/passport/' class='learn-more'><button class='learn-more'>LEARN MORE <i class='fa fa-arrow-circle-o-right'></i></button></a>
				</div>
			</div></div>";

    return '<div class="passportcoveplayer" data-window="'.$window.'" data-media="'.$tp_media_id.'"><div class="passport-gated-video"><img src="'.$large_thumb.'" >'.$passportOverlay . '</div></div>';
}

/* end of file */
