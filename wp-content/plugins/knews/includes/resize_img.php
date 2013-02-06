<?php 

function knews_resize_img_fn($url_img, $width, $height) {

	$wp_dirs = wp_upload_dir();
		
	$absolute_dir = substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], 'wp-admin'));

	$wp_dirs['basedir'] = substr($wp_dirs['basedir'], strpos($wp_dirs['basedir'], $absolute_dir));

	//echo '*' . $wp_dirs['baseurl'] . '*<br>';
	//echo '*' . substr($url_img, 0, strlen($wp_dirs['baseurl'])) . '*<br>';
	if (substr($url_img, 0, strlen($wp_dirs['baseurl'])) != $wp_dirs['baseurl']) {
		//echo 'no comencen igual<br>';
		$wp_dirs['baseurl']=substr($url_img, 0, strpos($url_img, 'wp-content'));
		$wp_dirs['basedir']=substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], 'wp-admin'));
	}
	//echo '*' . $wp_dirs['baseurl'] . '*<br>';
	//echo '*' . $wp_dirs['basedir'] . '*<br>';

	//$url_start = substr($url_img, 0, strpos($url_img, $_SERVER['SERVER_NAME']) + strlen($_SERVER['SERVER_NAME']));

	$pos = strrpos($url_img, "-");
	if ($pos !== false) { 
		$pos2 = strrpos($url_img, ".");
		
		if ($pos2 !== false) { 
			$try_original = substr($url_img, 0, $pos) . substr($url_img, $pos2);
			$try_original2 = substr($try_original, strlen($wp_dirs['baseurl']));

			if (is_file($wp_dirs['basedir'] . $try_original2)) $url_img = $try_original;
		}
	}
	
    if ($url_img != '' && $url_img != 'undefined') {

		// cut the url
		//$url_imatge = substr($img_url, strpos($img_url, 'wp-content'));

		$url_imatge = substr($url_img, strlen($wp_dirs['baseurl']));
		$url=$url_imatge;

		$url_imatge = str_replace('.jpg', '-' . $width . 'x' . $height .'.jpg', $url_imatge);
		$url_imatge = str_replace('.jpeg', '-' . $width . 'x' . $height .'.jpeg', $url_imatge);
		$url_imatge = str_replace('.gif', '-' . $width . 'x' . $height .'.gif', $url_imatge);
		$url_imatge = str_replace('.png', '-' . $width . 'x' . $height .'.png', $url_imatge);

		$url_imatge = str_replace('.JPG', '-' . $width . 'x' . $height .'.JPG', $url_imatge);
		$url_imatge = str_replace('.JPEG', '-' . $width . 'x' . $height .'.JPEG', $url_imatge);
		$url_imatge = str_replace('.GIF', '-' . $width . 'x' . $height .'.GIF', $url_imatge);
		$url_imatge = str_replace('.PNG', '-' . $width . 'x' . $height .'.PNG', $url_imatge);

		if (is_file($wp_dirs['basedir'] . $url)) {
			$size = getimagesize($wp_dirs['basedir'] . $url);
			if ($size[0]==$width && $size[1]==$height) {

				$jsondata['result'] = 'ok';
				$jsondata['url'] = $wp_dirs['baseurl'] . $url;
				return $jsondata;
			}
		}
		
		if (is_file($wp_dirs['basedir'] . $url_imatge)) {

			$jsondata['result'] = 'ok';
			$jsondata['url'] = $wp_dirs['baseurl'] . $url_imatge;
			return $jsondata;
	
		} else {
	
			// resize the image
			
			global $wp_version;
			if (version_compare('3.5', $wp_version, '<=')) {

				$image_editor = wp_get_image_editor( $wp_dirs['basedir'] . $url );
				if ( ! is_wp_error( $image_editor ) ) {

					$file_extension = pathinfo($wp_dirs['basedir'] . $url, PATHINFO_EXTENSION);
					$thumb = $wp_dirs['basedir'] . substr($url, 0, (strlen($file_extension) + 1) * -1) . '-' . $width.'x'.$height . '.' . $file_extension;

					$image_editor->resize( $width, $height, true );
					$image_editor->save( $thumb );

				} else {
					$jsondata['result'] = 'error';
					$jsondata['url'] = '';
					$jsondata['message'] = __('Error','knews') . ': ' . $image_editor->get_error_message();;
					return $jsondata;
				}

			} else {
				$thumb = image_resize($wp_dirs['basedir'] . $url, $width, $height, true, $width.'x'.$height);
				if ( is_wp_error( $thumb ) ) {
					$jsondata['result'] = 'error';
					$jsondata['url'] = '';
					$jsondata['message'] = __('Error','knews') . ': ' . $thumb->get_error_message();;
					return $jsondata;
				}
			}
			

			if (is_string($thumb)) {

				//$thumb = substr($thumb, strpos($thumb, 'wp-content'));
				$thumb = substr($thumb, strlen($wp_dirs['basedir']));

				$jsondata['result'] = 'ok';
				$jsondata['url'] = $wp_dirs['baseurl'] . $thumb;
				return $jsondata;
	
			} else {
				if (is_file($absolute_dir . $url)) {

					$jsondata['result'] = 'ok';
					$jsondata['url'] = $wp_dirs['baseurl'] . $url;
					return $jsondata;
					
				} else {

					$jsondata['result'] = 'error';
					$jsondata['url'] = '';
					$jsondata['message'] = __('Error','knews') . ': ' . __('Check the directory permissions for','knews') . ' ' . $wp_dirs['basedir'] . dirname($url);
					return $jsondata;
				}
			}
		}

	} else {

		$jsondata['result'] = 'error';
		$jsondata['url'] = '';
		$jsondata['message'] = __('Error: there is no image selected','knews');
		return $jsondata;
	}
}
?>