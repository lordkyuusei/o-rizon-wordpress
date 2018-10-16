<?php
class utilsPts {
    static public function jsonEncode($arr) {
        return (is_array($arr) || is_object($arr)) ? json_encode_utf_normal($arr) : json_encode_utf_normal(array());
    }
    static public function jsonDecode($str) {
        if(is_array($str))
            return $str;
        if(is_object($str))
            return (array)$str;
        return empty($str) ? array() : json_decode($str, true);
    }
    static public function unserialize($data, $safe = false) {
        return $safe ? @unserialize($data) : unserialize($data);
    }
    static public function serialize($data) {
        return serialize($data);
    }
    static public function createDir($path, $params = array('chmod' => NULL, 'httpProtect' => false)) {
        if(@mkdir($path)) {
            if(!is_null($params['chmod'])) {
                @chmod($path, $params['chmod']);
            }
            if(!empty($params['httpProtect'])) {
                self::httpProtectDir($path);
            }
            return true;
        }
        return false;
    }
    static public function httpProtectDir($path) {
        $content = 'DENY FROM ALL';
        if(strrpos($path, DS) != strlen($path))
            $path .= DS;
        if(file_put_contents($path. '.htaccess', $content)) {
            return true;
        }
        return false;
    }
    /**
     * Copy all files from one directory ($source) to another ($destination)
     * @param string $source path to source directory
     * @params string $destination path to destination directory
     */
    static public function copyDirectories($source, $destination) {
        if(is_dir($source)) {
            @mkdir($destination);
            $directory = dir($source);
            while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
                if ( $readdirectory == '.' || $readdirectory == '..' ) {
                    continue;
                }
                $PathDir = $source . '/' . $readdirectory; 
                if (is_dir($PathDir)) {
                    utilsPts::copyDirectories( $PathDir, $destination . '/' . $readdirectory );
                    continue;
                }
                copy( $PathDir, $destination . '/' . $readdirectory );
            }
            $directory->close();
        } else {
            copy( $source, $destination );
        }
    }
    static public function getIP() {
        return (empty($_SERVER['HTTP_CLIENT_IP']) ? (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']) : $_SERVER['HTTP_CLIENT_IP']);
    }
    
    /**
     * Parse xml file into simpleXML object
     * @param string $path path to xml file
     * @return mixed object SimpleXMLElement if success, else - false
     */
    static public function getXml($path) {
        if(is_file($path)) {
            return simplexml_load_file($path);
        }
        return false;
    }
    /**
     * Check if the element exists in array
     * @param array $param 
     */
    static public function xmlAttrToStr($param, $element) {
        if (isset($param[$element])) {
            // convert object element to string
            return (string)$param[$element];
        } else {
            return '';
        }
    }
    static public function xmlNodeAttrsToArr($node) {
        $arr = array();
        foreach($node->attributes() as $a => $b) {
            $arr[$a] = utilsPts::xmlAttrToStr($node, $a);
        }
        return $arr;
    }
    static public function deleteFile($str) {
        return @unlink($str);
    }
    static public function deleteDir($str){
        if(is_file($str)){
            return self::deleteFile($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                utilsPts::deleteDir($path);
            }
            return @rmdir($str);
        }
    }
    /**
     * Retrives list of directories ()
     */
    static public function getDirList($path) {
        $res = array();
        if(is_dir($path)){
            $files = scandir($path);
            foreach($files as $f) {
                if($f == '.' || $f == '..' || $f == '.svn') continue;
                if(!is_dir($path. $f))                      continue;
                $res[$f] = array('path' => $path. $f. DS);
            }
        }
        return $res;
    }
    /**
     * Retrives list of files
     */
    static public function getFilesList($path) {
        $files = array();
        if(is_dir($path)){
            $dirHandle = opendir($path);
            while(($file = readdir($dirHandle)) !== false) {
                if($file != '.' && $file != '..' && $f != '.svn' && is_file($path. DS. $file)) {
                    $files[] = $file;
                }
            }
        }
        return $files;
    }
    /**
     * Check if $var is object or something another in future
     */
    static public function is($var, $what = '') {
        if (!is_object($var)) {
            return false;
        }
        if(get_class($var) == $what) {
            return true;
        }
        return false;
    }
    /**
     * Get array with all monthes of year, uses in paypal pro and sagepay payment modules for now, than - who knows)
     * @return array monthes
     */
    static public function getMonthesArray() {
        static $monthsArray = array();
        //Some cache
        if(!empty($monthsArray))
            return $monthsArray;
        for ($i=1; $i<13; $i++) {
            $monthsArray[sprintf('%02d', $i)] = strftime('%B', mktime(0,0,0,$i,1,2000));
        }
        return $monthsArray;
    }
    /**
     * Get an array with years range from current year
     * @param int $from - how many years from today ago
     * @param int $to - how many years in future
     * @param $formatKey - format for keys in array, @see strftime
     * @param $formatVal - format for values in array, @see strftime
     * @return array - years 
     */
    static public function getYearsArray($from, $to, $formatKey = '%Y', $formatVal = '%Y') {
        $today = getdate();
        $yearsArray = array();
        for ($i=$today['year']-$from; $i <= $today['year']+$to; $i++) {
            $yearsArray[strftime($formatKey,mktime(0,0,0,1,1,$i))] = strftime($formatVal,mktime(0,0,0,1,1,$i));
        }
        return $yearsArray;
    }
    /**
     * Make replacement in $text, where it will be find all keys with prefix ":" and replace it with corresponding value
     * @see email_templatesModel::renderContent()
     * @see checkoutView::getSuccessPage()
     */
    static public function makeVariablesReplacement($text, $variables) {
        if(!empty($text) && !empty($variables) && is_array($variables)) {
            foreach($variables as $k => $v) {
                $text = str_replace(':'. $k, $v, $text);
            }
            return $text;
        }
        return false;
    }
    /**
     * Retrive full directory of plugin
     * @param string $name - plugin name
     * @return string full path in file system to plugin directory
     */
    static public function getPluginDir($name = '') {
        return WP_PLUGIN_DIR. DS. $name. DS;
    }
    static public function getPluginPath($name = '') {
        return plugins_url(). '/'. $name. '/';
    }
    static public function getExtModDir($plugName) {
		return self::getPluginDir($plugName);
    }
    static public function getExtModPath($plugName) {
        return self::getPluginPath($plugName);
    }
    static public function getCurrentWPThemePath() {
        return get_template_directory_uri();
    }
    static public function isThisCommercialEdition() {
        /*$commercialModules = array('rating');
        foreach($commercialModules as $m) {
            if(!framePts::_()->getModule($m)) 
                return false;
            if(!is_dir(framePts::_()->getModule($m)->getModDir())) 
                return false;
        }
        return true;*/
        foreach(framePts::_()->getModules() as $m) {
            if(is_object($m) && $m->isExternal()) // Should be at least one external module
                return true;
        }
        return false;
    }
    static public function checkNum($val, $default = 0) {
        if(!empty($val) && is_numeric($val))
            return $val;
        return $default;
    }
    static public function checkString($val, $default = '') {
        if(!empty($val) && is_string($val))
            return $val;
        return $default;
    }
    /**
     * Retrives extension of file
     * @param string $path - path to a file
     * @return string - file extension
     */
    static public function getFileExt($path) {
        return strtolower( pathinfo($path, PATHINFO_EXTENSION) );
    }
    static public function getRandStr($length = 10, $allowedChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890', $params = array()) {
        $result = '';
        $allowedCharsLen = strlen($allowedChars);
		if(isset($params['only_lowercase']) && $params['only_lowercase']) {
			$allowedChars = strtolower($allowedChars);
		}
        while(strlen($result) < $length) {
          $result .= substr($allowedChars, rand(0, $allowedCharsLen), 1);
        }

        return $result;
    }
    /**
     * Get current host location
     * @return string host string
     */
    static public function getHost() {
        return $_SERVER['HTTP_HOST'];
    }
    /**
     * Check if device is mobile
     * @return bool true if user are watching this site from mobile device
     */
    static public function isMobile() {
        return mobileDetect::_()->isMobile();
    }
    /**
     * Check if device is tablet
     * @return bool true if user are watching this site from tablet device
     */
    static public function isTablet() {
        return mobileDetect::_()->isTablet();
    }
    static public function getUploadsDir() {
        $uploadDir = wp_upload_dir();
        return $uploadDir['basedir'];
    }
    static public function getUploadsPath() {
        $uploadDir = wp_upload_dir();
        return $uploadDir['baseurl'];
    }
    static public function arrToCss($data) {
        $res = '';
        if(!empty($data)) {
            foreach($data as $k => $v) {
                $res .= $k. ':'. $v. ';';
            }
        }
        return $res;
    }
    /**
     * Activate all CSP Plugins
     * 
     * @return NULL Check if it's site or multisite and activate.
     */
    static public function activatePlugin() {
        global $wpdb;
		if(PTS_TEST_MODE) {
			add_action('activated_plugin', array(framePts::_(), 'savePluginActivationErrors'));
		}
        if (function_exists('is_multisite') && is_multisite()) {
			// $orig_id = $wpdb->blogid;
            $blog_id = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_id as $id) {
                if (switch_to_blog($id)) {
                    installerPts::init();
					restore_current_blog();
                } 
            }
			// restore_current_blog();
			// switch_to_blog($orig_id);
            return;
        } else {
            installerPts::init();
        }
    }

    /**
     * Delete All CSP Plugins
     * 
     * @return NULL Check if it's site or multisite and decativate it.
     */
    static public function deletePlugin() {
        global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
			// $orig_id = $wpdb->blogid;
            $blog_id = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_id as $id) {
                if (switch_to_blog($id)) {
                    installerPts::delete();
					restore_current_blog();
                }
            }
			// restore_current_blog();
			// switch_to_blog($orig_id);
            return;
        } else {
            installerPts::delete();
        }
    }
	static public function deactivatePlugin() {
		global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
			// $orig_id = $wpdb->blogid;
            $blog_id = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_id as $id) {
                if (switch_to_blog($id)) {
                    installerPts::deactivate();
					restore_current_blog();
                } 
            }
			// restore_current_blog();
			// switch_to_blog($orig_id);
            return;
        } else {
            installerPts::deactivate();
        }
	}
	
	static public function isWritable($filename) {
		return is_writable($filename);
	}
	
	static public function isReadable($filename) {
		return is_readable($filename);
	}
	
	static public function fileExists($filename) {
		return file_exists($filename);
	}
	static public function isPluginsPage() {
		return (basename(reqPts::getVar('SCRIPT_NAME', 'server')) === 'plugins.php');
	}
	static public function isSessionStarted() {
		if(version_compare(PHP_VERSION, '5.4.0') >= 0 && function_exists('session_status')) {
			return !(session_status() == PHP_SESSION_NONE);
		} else {
			return !(session_id() == '');
		}
	}
	static public function generateBgStyle($data) {
		$stageBgStyles = array();
		$stageBgStyle = '';
		switch($data['type']) {
			case 'color':
				$stageBgStyles[] = 'background-color: '. $data['color'];
				$stageBgStyles[] = 'opacity: '. $data['opacity'];
				break;
			case 'img':
				$stageBgStyles[] = 'background-image: url('. $data['img']. ')';
				switch($data['img_pos']) {
					case 'center':
						$stageBgStyles[] = 'background-repeat: no-repeat';
						$stageBgStyles[] = 'background-position: center center';
						break;
					case 'tile':
						$stageBgStyles[] = 'background-repeat: repeat';
						break;
					case 'stretch':
						$stageBgStyles[] = 'background-repeat: no-repeat';
						$stageBgStyles[] = '-moz-background-size: 100% 100%';
						$stageBgStyles[] = '-webkit-background-size: 100% 100%';
						$stageBgStyles[] = '-o-background-size: 100% 100%';
						$stageBgStyles[] = 'background-size: 100% 100%';
						break;
				}
				break;
		}
		if(!empty($stageBgStyles)) {
			$stageBgStyle = implode(';', $stageBgStyles);
		}
		return $stageBgStyle;
	}
	/**
	 * Parse worptsess post/page/custom post type content for images and return it's IDs if there are images
	 * @param string $content Post/page/custom post type content
	 * @return array List of images IDs from content
	 */
	static public function parseImgIds($content) {
		$res = array();
		preg_match_all('/wp-image-(?<ID>\d+)/', $content, $matches);
		if($matches && isset($matches['ID']) && !empty($matches['ID'])) {
			$res = $matches['ID'];
		}
		return $res;
	}
	/**
	 * Retrive file path in file system from provided URL, it should be in wp-content/uploads
	 * @param string $url File url path, should be in wp-content/uploads
	 * @return string Path in file system to file
	 */
	static public function getUploadFilePathFromUrl($url) {
		$uploadsPath = self::getUploadsPath();
		$uploadsDir = self::getUploadsDir();
		return str_replace($uploadsPath, $uploadsDir, $url);
	}
	/**
	 * Retrive file URL from provided file system path, it should be in wp-content/uploads
	 * @param string $path File path, should be in wp-content/uploads
	 * @return string URL to file
	 */
	static public function getUploadUrlFromFilePath($path) {
		$uploadsPath = self::getUploadsPath();
		$uploadsDir = self::getUploadsDir();
		return str_replace($uploadsDir, $uploadsPath, $path);
	}
	static public function getUserBrowserString() {
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : false;
	}
	static public function getBrowser() {
		$u_agent = self::getUserBrowserString();
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version = '';
		$pattern = '';
		
		if($u_agent) {
			//First get the platform?
			if (preg_match('/linux/i', $u_agent)) {
				$platform = 'linux';
			}
			elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
				$platform = 'mac';
			}
			elseif (preg_match('/windows|win32/i', $u_agent)) {
				$platform = 'windows';
			}
			// Next get the name of the useragent yes seperately and for good reason
			if((preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) || (strpos($u_agent, 'Trident/7.0; rv:11.0') !== false))
			{
				$bname = 'Internet Explorer';
				$ub = "MSIE";
			}
			elseif(preg_match('/Firefox/i',$u_agent))
			{
				$bname = 'Mozilla Firefox';
				$ub = "Firefox";
			}
			elseif(preg_match('/Chrome/i',$u_agent))
			{
				$bname = 'Google Chrome';
				$ub = "Chrome";
			}
			elseif(preg_match('/Safari/i',$u_agent))
			{
				$bname = 'Apple Safari';
				$ub = "Safari";
			}
			elseif(preg_match('/Opera/i',$u_agent))
			{
				$bname = 'Opera';
				$ub = "Opera";
			}
			elseif(preg_match('/Netscape/i',$u_agent))
			{
				$bname = 'Netscape';
				$ub = "Netscape";
			}

			// finally get the correct version number
			$known = array('Version', $ub, 'other');
			$pattern = '#(?<browser>' . join('|', $known) .
			')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
			if (!preg_match_all($pattern, $u_agent, $matches)) {
				// we have no matching number just continue
			}

			// see how many we have
			$i = count($matches['browser']);
			if ($i != 1) {
				//we will have two since we are not using 'other' argument yet
				//see if version is before or after the name
				if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
					$version= $matches['version'][0];
				}
				else {
					$version= $matches['version'][1];
				}
			}
			else {
				$version= $matches['version'][0];
			}
		}

		// check if we have a number
		if ($version==null || $version=="") {$version="?";}

		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'    => $pattern
		);
	}
	static public function getBrowsersList() {
		return array(
			'Unknown', 'Internet Explorer', 'Mozilla Firefox', 'Google Chrome', 'Apple Safari', 
			'Opera', 'Netscape',
		);
	}
	static public function getLangCode2Letter() {
		$langCode = self::getLangCode();
		return strlen($langCode) > 2 ? substr($langCode, 0, 2) : $langCode;
	}
	static public function getLangCode() {
		return get_locale();
	}
	static public function rgbToArray($rgb) {
		$rgb = array_map('trim', 
				explode(',', 
					trim(str_replace(array('rgb', 'a', '(', ')'), '', $rgb))));
		return $rgb;
	}
	static public function rgbToHex($rgb) {
		if(is_string($rgb)) {
			$rgb = self::rgbToArray($rgb);
		}
		$hex = "#";
		$hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

		return $hex;
	}
	static public function hexToRgb($hex) {
		if(strpos($hex, 'rgb') !== false) {	// Maybe it's already in rgb format - just return it as array
			return self::rgbToArray($hex);
		}
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1). substr($hex,0,1));
			$g = hexdec(substr($hex,1,1). substr($hex,1,1));
			$b = hexdec(substr($hex,2,1). substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		//return implode(",", $rgb); // returns the rgb values separated by commas
		return $rgb; // returns an array with the rgb values
	}
	static public function getFontsList() {
		return array("ABeeZee","Abel","Abril Fatface","Aclonica","Acme","Actor","Adamina","Advent Pro","Aguafina Script","Akronim","Aladin","Aldrich","Alef","Alegreya","Alegreya SC","Alegreya Sans","Alegreya Sans SC","Alex Brush","Alfa Slab One","Alice","Alike","Alike Angular","Allan","Allerta","Allerta Stencil","Allura","Almendra","Almendra Display","Almendra SC","Amarante","Amaranth","Amatic SC","Amethysta","Amiri","Anaheim","Andada","Andika","Angkor","Annie Use Your Telescope","Anonymous Pro","Antic","Antic Didone","Antic Slab","Anton","Arapey","Arbutus","Arbutus Slab","Architects Daughter","Archivo Black","Archivo Narrow","Arimo","Arizonia","Armata","Artifika","Arvo","Asap","Asset","Astloch","Asul","Atomic Age","Aubrey","Audiowide","Autour One","Average","Average Sans","Averia Gruesa Libre","Averia Libre","Averia Sans Libre","Averia Serif Libre","Bad Script","Balthazar","Bangers","Basic","Battambang","Baumans","Bayon","Belgrano","Belleza","BenchNine","Bentham","Berkshire Swash","Bevan","Bigelow Rules","Bigshot One","Bilbo","Bilbo Swash Caps","Biryani","Bitter","Black Ops One","Bokor","Bonbon","Boogaloo","Bowlby One","Bowlby One SC","Brawler","Bree Serif","Bubblegum Sans","Bubbler One","Buda","Buenard","Butcherman","Butterfly Kids","Cabin","Cabin Condensed","Cabin Sketch","Caesar Dressing","Cagliostro","Calligraffitti","Cambay","Cambo","Candal","Cantarell","Cantata One","Cantora One","Capriola","Cardo","Carme","Carrois Gothic","Carrois Gothic SC","Carter One","Caudex","Cedarville Cursive","Ceviche One","Changa One","Chango","Chau Philomene One","Chela One","Chelsea Market","Chenla","Cherry Cream Soda","Cherry Swash","Chewy","Chicle","Chivo","Cinzel","Cinzel Decorative","Clicker Script","Coda","Coda Caption","Codystar","Combo","Comfortaa","Coming Soon","Concert One","Condiment","Content","Contrail One","Convergence","Cookie","Copse","Corben","Courgette","Cousine","Coustard","Covered By Your Grace","Crafty Girls","Creepster","Crete Round","Crimson Text","Croissant One","Crushed","Cuprum","Cutive","Cutive Mono","Damion","Dancing Script","Dangrek","Dawning of a New Day","Days One","Dekko","Delius","Delius Swash Caps","Delius Unicase","Della Respira","Denk One","Devonshire","Dhurjati","Didact Gothic","Diplomata","Diplomata SC","Domine","Donegal One","Doppio One","Dorsa","Dosis","Dr Sugiyama","Droid Sans","Droid Sans Mono","Droid Serif","Duru Sans","Dynalight","EB Garamond","Eagle Lake","Eater","Economica","Ek Mukta","Electrolize","Elsie","Elsie Swash Caps","Emblema One","Emilys Candy","Engagement","Englebert","Enriqueta","Erica One","Esteban","Euphoria Script","Ewert","Exo","Exo 2","Expletus Sans","Fanwood Text","Fascinate","Fascinate Inline","Faster One","Fasthand","Fauna One","Federant","Federo","Felipa","Fenix","Finger Paint","Fira Mono","Fira Sans","Fjalla One","Fjord One","Flamenco","Flavors","Fondamento","Fontdiner Swanky","Forum","Francois One","Freckle Face","Fredericka the Great","Fredoka One","Freehand","Fresca","Frijole","Fruktur","Fugaz One","GFS Didot","GFS Neohellenic","Gabriela","Gafata","Galdeano","Galindo","Gentium Basic","Gentium Book Basic","Geo","Geostar","Geostar Fill","Germania One","Gidugu","Gilda Display","Give You Glory","Glass Antiqua","Glegoo","Gloria Hallelujah","Goblin One","Gochi Hand","Gorditas","Goudy Bookletter 1911","Graduate","Grand Hotel","Gravitas One","Great Vibes","Griffy","Gruppo","Gudea","Gurajada","Habibi","Halant","Hammersmith One","Hanalei","Hanalei Fill","Handlee","Hanuman","Happy Monkey","Headland One","Henny Penny","Herr Von Muellerhoff","Hind","Holtwood One SC","Homemade Apple","Homenaje","IM Fell DW Pica","IM Fell DW Pica SC","IM Fell Double Pica","IM Fell Double Pica SC","IM Fell English","IM Fell English SC","IM Fell French Canon","IM Fell French Canon SC","IM Fell Great Primer","IM Fell Great Primer SC","Iceberg","Iceland","Imprima","Inconsolata","Inder","Indie Flower","Inika","Irish Grover","Istok Web","Italiana","Italianno","Jacques Francois","Jacques Francois Shadow","Jaldi","Jim Nightshade","Jockey One","Jolly Lodger","Josefin Sans","Josefin Slab","Joti One","Judson","Julee","Julius Sans One","Junge","Jura","Just Another Hand","Just Me Again Down Here","Kalam","Kameron","Kantumruy","Karla","Karma","Kaushan Script","Kavoon","Kdam Thmor","Keania One","Kelly Slab","Kenia","Khand","Khmer","Khula","Kite One","Knewave","Kotta One","Koulen","Kranky","Kreon","Kristi","Krona One","Kurale","La Belle Aurore","Laila","Lakki Reddy","Lancelot","Lateef","Lato","League Script","Leckerli One","Ledger","Lekton","Lemon","Libre Baskerville","Life Savers","Lilita One","Lily Script One","Limelight","Linden Hill","Lobster","Lobster Two","Londrina Outline","Londrina Shadow","Londrina Sketch","Londrina Solid","Lora","Love Ya Like A Sister","Loved by the King","Lovers Quarrel","Luckiest Guy","Lusitana","Lustria","Macondo","Macondo Swash Caps","Magra","Maiden Orange","Mako","Mallanna","Mandali","Marcellus","Marcellus SC","Marck Script","Margarine","Marko One","Marmelad","Martel","Martel Sans","Marvel","Mate","Mate SC","Maven Pro","McLaren","Meddon","MedievalSharp","Medula One","Megrim","Meie Script","Merienda","Merienda One","Merriweather","Merriweather Sans","Metal","Metal Mania","Metamorphous","Metrophobic","Michroma","Milonga","Miltonian","Miltonian Tattoo","Miniver","Miss Fajardose","Modak","Modern Antiqua","Molengo","Molle","Monda","Monofett","Monoton","Monsieur La Doulaise","Montaga","Montez","Montserrat","Montserrat Alternates","Montserrat Subrayada","Moul","Moulpali","Mountains of Christmas","Mouse Memoirs","Mr Bedfort","Mr Dafoe","Mr De Haviland","Mrs Saint Delafield","Mrs Sheppards","Muli","Mystery Quest","NTR","Neucha","Neuton","New Rocker","News Cycle","Niconne","Nixie One","Nobile","Nokora","Norican","Nosifer","Nothing You Could Do","Noticia Text","Noto Sans","Noto Serif","Nova Cut","Nova Flat","Nova Mono","Nova Oval","Nova Round","Nova Script","Nova Slim","Nova Square","Numans","Nunito","Odor Mean Chey","Offside","Old Standard TT","Oldenburg","Oleo Script","Oleo Script Swash Caps","Open Sans","Open Sans Condensed","Oranienbaum","Orbitron","Oregano","Orienta","Original Surfer","Oswald","Over the Rainbow","Overlock","Overlock SC","Ovo","Oxygen","Oxygen Mono","PT Mono","PT Sans","PT Sans Caption","PT Sans Narrow","PT Serif","PT Serif Caption","Pacifico","Palanquin","Palanquin Dark","Paprika","Parisienne","Passero One","Passion One","Pathway Gothic One","Patrick Hand","Patrick Hand SC","Patua One","Paytone One","Peddana","Peralta","Permanent Marker","Petit Formal Script","Petrona","Philosopher","Piedra","Pinyon Script","Pirata One","Plaster","Play","Playball","Playfair Display","Playfair Display SC","Podkova","Poiret One","Poller One","Poly","Pompiere","Pontano Sans","Port Lligat Sans","Port Lligat Slab","Pragati Narrow","Prata","Preahvihear","Press Start 2P","Princess Sofia","Prociono","Prosto One","Puritan","Purple Purse","Quando","Quantico","Quattrocento","Quattrocento Sans","Questrial","Quicksand","Quintessential","Qwigley","Racing Sans One","Radley","Rajdhani","Raleway","Raleway Dots","Ramabhadra","Ramaraja","Rambla","Rammetto One","Ranchers","Rancho","Ranga","Rationale","Ravi Prakash","Redressed","Reenie Beanie","Revalia","Ribeye","Ribeye Marrow","Righteous","Risque","Roboto","Roboto Condensed","Roboto Slab","Rochester","Rock Salt","Rokkitt","Romanesco","Ropa Sans","Rosario","Rosarivo","Rouge Script","Rozha One","Rubik Mono One","Rubik One","Ruda","Rufina","Ruge Boogie","Ruluko","Rum Raisin","Ruslan Display","Russo One","Ruthie","Rye","Sacramento","Sail","Salsa","Sanchez","Sancreek","Sansita One","Sarina","Sarpanch","Satisfy","Scada","Scheherazade","Schoolbell","Seaweed Script","Sevillana","Seymour One","Shadows Into Light","Shadows Into Light Two","Shanti","Share","Share Tech","Share Tech Mono","Shojumaru","Short Stack","Siemreap","Sigmar One","Signika","Signika Negative","Simonetta","Sintony","Sirin Stencil","Six Caps","Skranji","Slabo 13px","Slabo 27px","Slackey","Smokum","Smythe","Sniglet","Snippet","Snowburst One","Sofadi One","Sofia","Sonsie One","Sorts Mill Goudy","Source Code Pro","Source Sans Pro","Source Serif Pro","Special Elite","Spicy Rice","Spinnaker","Spirax","Squada One","Sree Krushnadevaraya","Stalemate","Stalinist One","Stardos Stencil","Stint Ultra Condensed","Stint Ultra Expanded","Stoke","Strait","Sue Ellen Francisco","Sumana","Sunshiney","Supermercado One","Suranna","Suravaram","Suwannaphum","Swanky and Moo Moo","Syncopate","Tangerine","Taprom","Tauri","Teko","Telex","Tenali Ramakrishna","Tenor Sans","Text Me One","The Girl Next Door","Tienne","Timmana","Tinos","Titan One","Titillium Web","Trade Winds","Trocchi","Trochut","Trykker","Tulpen One","Ubuntu","Ubuntu Condensed","Ubuntu Mono","Ultra","Uncial Antiqua","Underdog","Unica One","UnifrakturCook","UnifrakturMaguntia","Unkempt","Unlock","Unna","VT323","Vampiro One","Varela","Varela Round","Vast Shadow","Vesper Libre","Vibur","Vidaloka","Viga","Voces","Volkhov","Vollkorn","Voltaire","Waiting for the Sunrise","Wallpoet","Walter Turncoat","Warnes","Wellfleet","Wendy One","Wire One","Yanone Kaffeesatz","Yellowtail","Yeseva One","Yesteryear","Zeyada");
	}
	static public function getStandardFontsList() {
		return array("Georgia","Palatino Linotype","Times New Roman","Arial","Helvetica","Arial Black","Gadget","Comic Sans MS","Impact","Charcoal","Lucida Sans Unicode","Lucida Grande","Tahoma","Geneva","Trebuchet MS","Verdana","Geneva","Courier New","Courier","Lucida Console","Monaco");
	}
}
