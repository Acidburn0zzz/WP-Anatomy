<?php
//User can enter e-mail for login
add_filter('authenticate', 'wpa_allow_email_login', 20, 3);
function wpa_allow_email_login( $user, $username, $password ) {
    if ( is_email( $username ) ) {
        $user = get_user_by( 'email', $username );
        if ( $user ) $username = $user->user_login;
    }
    return wp_authenticate_username_password(null, $username, $password );
}

add_filter( 'gettext', 'addEmailToLogin', 20, 3 );
function addEmailToLogin( $translated_text, $text, $domain ) {
    if ( "Username" == $translated_text )
        $translated_text .= __( ' Or Email');
    return $translated_text;
}

if(defined('GOOGLEMAPS')) {
    /* google map shortcode
        *** Using [googlemap id="somemapid" coordinates="1 ,1" zoom="17" height="500px" infobox="<p>Some Infobox Content</p>"]
        *** if need street view, please add 'streetview="true"';
        *** if you need satellite view in 45 angle add 'tilt="45"';
    */
    function google_map_js($atts, $content) {
        extract(shortcode_atts(array(
            'id'                => 'map_canvas',
            'coordinates'       => '1, 1',
            'zoom'              => 15,
            'height'            => '350px',
            'zoomcontrol'       => 'false',
            'scrollwheel'       => 'false',
            'scalecontrol'      => 'false',
            'disabledefaultui'  => 'false',
            'infobox'           => '',
            'satellite'         => '',
            'tilt'              => '',
            'icon'              => theme().'/images/marker.png',
            'streetview'        => ''
        ), $atts));
        $mapid = str_replace('-','_',$id);

        $map = !$streetview?'<div class="googlemap" id="'.$id.'" '.($height?'style="height:'.$height.'"':'').'></div><script>
    var '.$mapid.';
    function initialize_'.$mapid.'() {
        var myLatlng = new google.maps.LatLng('.$coordinates.');
        var mapOptions = {
            '.($satellite?'mapTypeId: google.maps.MapTypeId.SATELLITE,':'').'
            zoom: '.$zoom.',
            center: myLatlng,
            zoomControl: '.$zoomcontrol.',
            scrollwheel: '.$scrollwheel.',
            scaleControl: '.$scalecontrol.',
            disableDefaultUI: '.$disabledefaultui.'
            '.( $content ? ',styles:'.$content : '' ).'
        };
        var '.$mapid.' = new google.maps.Map(document.getElementById("'.$id.'"), mapOptions);
        '.($tilt?$mapid.'.setTilt(45);':'').'
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: '.$mapid.',
            '.($icon?'icon:"'.$icon.'",':'').'
            animation: google.maps.Animation.DROP
        });
        '.($infobox?'marker.info = new google.maps.InfoWindow({content: \''.$infobox.'\'});
        google.maps.event.addListener(marker, "click", function() {marker.info.open('.$mapid.', marker);});':'').'

        google.maps.event.addListener('.$mapid.', "center_changed", function() {
            window.setTimeout(function() {
                '.$mapid.'.panTo(marker.getPosition());
            }, 15000);
        });
    };
    google.maps.event.addDomListener(window, "load", initialize_'.$mapid.');
    </script>':do_streetView_map($id, $coordinates, $height, $streetview);
        return $map;
    }
    add_shortcode('googlemap', 'google_map_js');

    function do_streetView_map($id, $pos, $height, $streetview){
        return '<div class="googlemap" id="street_'.$id.'" '.($height?'style="height:'.$height.'"':'').'></div><script>
        function street_init_'.$id.'() {


        var geocoder =  new google.maps.Geocoder();
        geocoder.geocode( { "address": "'.$streetview.'" }, function(results, status) {
            var lookTo = results[0].geometry.location;
            if (status == google.maps.GeocoderStatus.OK) {
                  var panoOptions = {
                    position: lookTo,
                    panControl: false,
                    addressControl: false,
                    linksControl: false,
                    zoomControlOptions: false
                  };
                  var pano = new  google.maps.StreetViewPanorama(document.getElementById("street_'.$id.'"),panoOptions);
                  var service = new google.maps.StreetViewService;
                  service.getPanoramaByLocation(pano.getPosition(), 50, function(panoData) {
                    if (panoData != null) {
                      var panoCenter = panoData.location.latLng;
                      var heading = google.maps.geometry.spherical.computeHeading(panoCenter, lookTo);
                      var pov = pano.getPov();
                      pov.heading = heading;
                      pano.setPov(pov);
                      var marker = new google.maps.Marker({
                        map: pano,
                        position: lookTo
                      });
                    } else {
                      alert("Not Found");
                    }
                  });
            } else {
                alert("Could not find your address");
            }
        });
        }
        google.maps.event.addDomListener(window, "load", street_init_'.$id.');</script>';
    }
} //end GOOGLEMAPS

function content_btn($atts,$content){
    extract(shortcode_atts(array(
        'text' => 'Learn More',
        'link' => site_url(),
        'class' => false,
        'target' => false
    ), $atts ));
    return '<a href="' . $link . '" class="button'.($class?' '.$class:'').'" '.($target?'target="'.$target.'"':'').'>' . $text . '</a>';
}
add_shortcode("button", "content_btn");

function tree_children($absolute = false, $page_id = 0) {
    global $wp_query;
    if ($wp_query->is_posts_page) {
        $post = get_post(BLOG_ID);
    } else {
        global $post;
    }
    $ex_pages = null;
    $ex_args = array(
        'posts_per_page' => -1,
        'post_type' => 'page',
        'meta_key' => 'hide_page',
        'meta_value' => true
    );
    $excluded = new WP_Query($ex_args);
    if ($excluded->have_posts()): while ($excluded->have_posts()) : $excluded->the_post();
    $ex_pages .= get_the_ID() . ',';
    endwhile;
    $ex_pages = substr($ex_pages, 0, -1);
    endif;
    wp_reset_query();
    $childlist = get_pages('child_of=' . $post->ID . ($ex_pages ? '&exclude=' . $ex_pages : ''));
    $children = '';
    if ($post->post_parent) {
        $ancestors = get_post_ancestors($post->ID);
        $reverse = array_reverse($ancestors);
        $abs = $reverse[0];
        $children .= '<ul class="submenu">';
        $children .= wp_list_pages("title_li=&child_of=" . $abs . "&echo=0&sort_column=menu_order" . ($ex_pages ? '&exclude=' . $ex_pages : ''));
        $children .= '</ul>';
        echo $children;
    } elseif ($childlist) {
        echo '<ul class="submenu">' . wp_list_pages("title_li=&child_of=" . $post->ID . "&echo=0&sort_column=menu_order" . ($ex_pages ? '&exclude=' . $ex_pages : '')) . '</ul>';
    }
}

//remove <p> and <br /> from shortcodes
add_filter('the_content', 'shortcode_empty_paragraph_fix');
function shortcode_empty_paragraph_fix($content){
    $array = array (
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    );
    $content = strtr($content, $array);
    return $content;
}

function socialist(){
    $soci = get_field('socialist', 'option');
    $soc = '';
    if($soci) {
        $soc .= '<div class="socialist">';
        foreach($soci as $so) {
            $soc .= '<a class="icon-'.$so['icon'].'" target="_blank" href="'.$so['link'].'" rel="nofollow"></a>';
        }
        $soc .= '</div>';
    }
    return $soc;
}
add_shortcode("social", "socialist");

/*
    Base64 image Placeholder generator
*/

class PictureThis {
    const DEFAULT_BG = '#f5f5f5';
    const DEFAULT_FG = '#FFF';
    const DEFAULT_H = 100;
    const DEFAULT_W = 200;
    private static function _getRgb($color) {
        $color = str_replace('#', null, $color);
        if(strlen($color) == 3) {
            $r = substr($color, 0, 1);
            $g = substr($color, 1, 1);
            $b = substr($color, 2, 1);
            $color = $r.$r.$g.$g.$b.$b;
        }
        $rgb = array(
            'r' => hexdec(substr($color, 0, 2)),
            'g' => hexdec(substr($color, 2, 2)),
            'b' => hexdec(substr($color, 4, 2))
        );
        return $rgb;
    }
    public static function display(array $options = array()) {
        $width = self::DEFAULT_W;
        if(isset($options['w']) && is_numeric($options['w']) && $options['w'] > 0) {
            $width = $options['w'];
        }
        $height = self::DEFAULT_H;
        if(isset($options['h']) && is_numeric($options['h']) && $options['h'] > 0) {
            $height = $options['h'];
        }
        $image = imagecreate($width, $height);
        $text = "$width x $height";
        if(isset($options['t']) && $options['t']) {
            $text = $options['t'];
        }
        $bg = self::DEFAULT_BG;
        if(isset($options['bg'])) {
            $bg = $options['bg'];
        }
        $bg = self::_getRgb($bg);
        $bg_color = imagecolorallocate($image, $bg['r'], $bg['g'], $bg['b']);
        $fg = self::DEFAULT_FG;
        if(isset($options['fg'])) {
            $fg = $options['fg'];
        }
        $fg = self::_getRgb($fg);
        $fg_color = imagecolorallocate($image, $fg['r'], $fg['g'], $fg['b']);

        $text_width = imagefontwidth(5) * strlen($text);
        $center = ceil($width / 2);
        $x = $center - (ceil($text_width/2));
        $center = ceil($height / 2);
        $y = $center - 6;
        imagestring($image, 5, $x, $y, $text, $fg_color);

        ob_start();
        imagepng($image);
        $contents = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        return 'data:image/png;base64,'.base64_encode($contents);
    }
}

function placeImg($w = 1000, $h = 500, $bg = '#f5f5f5', $fg = '#bbbbbb', $t = 'Loading...'){
    $args = array();
    return PictureThis::display(
        array(
            'w'  => $w,
            'h'  => $h,
            't'  => $t,
            'bg' => $bg,
            'fg' => $fg
        )
    );
}