<?php

// https://www.dropbox.com/developers/apply?cont=/developers/apps

class UpdraftPlus_BackupModule_dropbox {

	private $current_file_hash;
	private $current_file_size;

	function chunked_callback($offset, $uploadid, $fullpath = false) {
		global $updraftplus;

		// Update upload ID
		$updraftplus->jobdata_set('updraf_dbid_'.$this->current_file_hash, $uploadid);
		$updraftplus->jobdata_set('updraf_dbof_'.$this->current_file_hash, $offset);

		if ($this->current_file_size > 0) {
			$percent = round(100*($offset/$this->current_file_size),1);
			$updraftplus->record_uploaded_chunk($percent, "$uploadid, $offset", $fullpath);
		} else {
			$updraftplus->log("Dropbox: Chunked Upload: $offset bytes uploaded");
			// This act is done by record_uploaded_chunk, and helps prevent overlapping runs
			touch($fullpath);
		}

	}

	function backup($backup_array) {

		global $updraftplus, $updraftplus_backup;
		$updraftplus->log("Dropbox: begin cloud upload");

		if (!function_exists('mcrypt_encrypt')) {
			$updraftplus->log('The mcrypt PHP module is not installed');
			$updraftplus->log(sprintf(__('The %s PHP module is not installed - ask your web hosting company to enable it', 'updraftplus'), 'mcrypt'), 'error');
			return false;
		}

		if (UpdraftPlus_Options::get_updraft_option('updraft_dropboxtk_request_token', 'xyz') == 'xyz') {
			$updraftplus->log('You do not appear to be authenticated with Dropbox');
			$updraftplus->log(__('You do not appear to be authenticated with Dropbox','updraftplus'), 'error');
			return false;
		}

		try {
			$dropbox = $this->bootstrap();
			$updraftplus->log("Dropbox: access gained");
			$dropbox->setChunkSize(524288); // 512Kb
		} catch (Exception $e) {
			$updraftplus->log('Dropbox error when trying to gain access: '.$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
			$updraftplus->log(sprintf(__('Dropbox error: %s (see log file for more)','updraftplus'), $e->getMessage()), 'error');
			return false;
		}

		$updraft_dir = $updraftplus->backups_dir_location();
		$dropbox_folder = trailingslashit(UpdraftPlus_Options::get_updraft_option('updraft_dropbox_folder'));

		foreach ($backup_array as $file) {

			$available_quota = -1;

			// If we experience any failures collecting account info, then carry on anyway
			try {

				$accountInfo = $dropbox->accountInfo();

				if ($accountInfo['code'] != "200") {
					$message = "Dropbox account/info did not return HTTP 200; returned: ". $accountInfo['code'];
				} elseif (!isset($accountInfo['body'])) {
					$message = "Dropbox account/info did not return the expected data";
				} else {
					$body = $accountInfo['body'];
					if (!isset($body->quota_info)) {
						$message = "Dropbox account/info did not return the expected data";
					} else {
						$quota_info = $body->quota_info;
						$total_quota = $quota_info->quota;
						$normal_quota = $quota_info->normal;
						$shared_quota = $quota_info->shared;
						$available_quota = $total_quota - ($normal_quota + $shared_quota);
						$message = "Dropbox quota usage: normal=".round($normal_quota/1048576,1)." Mb, shared=".round($shared_quota/1048576,1)." Mb, total=".round($total_quota/1048576,1)." Mb, available=".round($available_quota/1048576,1)." Mb";
					}
				}
				$updraftplus->log($message);
			} catch (Exception $e) {
				$updraftplus->log("Dropbox error: exception occurred whilst getting account info: ".$e->getMessage());
			}

			$file_success = 1;

			$hash = md5($file);
			$this->current_file_hash = $hash;

			$filesize = filesize($updraft_dir.'/'.$file);
			$this->current_file_size = $filesize;

			// We don't actually abort now - there's no harm in letting it try and then fail
			if ($available_quota != -1 && $available_quota < $filesize) {
				$updraftplus->log("File upload expected to fail: file ($file) size is $filesize b, whereas available quota is only $available_quota b");
				$updraftplus->log(sprintf(__("Account full: your %s account has only %d bytes left, but the file to be uploaded is %d bytes",'updraftplus'),'Dropbox', $available_quota, $filesize), 'error');
			}

			// Into Kb
			$filesize = $filesize/1024;
			$microtime = microtime(true);

			if ($upload_id = $updraftplus->jobdata_get('updraf_dbid_'.$hash)) {
				# Resume
				$offset =  $updraftplus->jobdata_get('updraf_dbof_'.$hash);
				$updraftplus->log("This is a resumption: $offset bytes had already been uploaded");
			} else {
				$offset = 0;
				$upload_id = null;
			}

			// Old-style, single file put: $put = $dropbox->putFile($updraft_dir.'/'.$file, $dropbox_folder.$file);

			$ourself = $this;

			$ufile = apply_filters('updraftplus_dropbox_modpath', $file);

			$updraftplus->log("Dropbox: Attempt to upload: $file to: $ufile");

			try {
				$dropbox->chunkedUpload($updraft_dir.'/'.$file, '', $ufile, true, $offset, $upload_id, array($ourself, 'chunked_callback'));
			} catch (Exception $e) {
				$updraftplus->log("Dropbox chunked upload exception: ".$e->getMessage());
				if (preg_match("/Submitted input out of alignment: got \[(\d+)\] expected \[(\d+)\]/i", $e->getMessage(), $matches)) {
					// Try the indicated offset
					$we_tried = $matches[1];
					$dropbox_wanted = $matches[2];
					$updraftplus->log("Dropbox not yet aligned: tried=$we_tried, wanted=$dropbox_wanted; will attempt recovery");
					try {
						$dropbox->chunkedUpload($updraft_dir.'/'.$file, '', $ufile, true, $dropbox_wanted, $upload_id, array($ourself, 'chunked_callback'));
					} catch (Exception $e) {
						$updraftplus->log('Dropbox error: '.$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
						$updraftplus->log('Dropbox '.sprintf(__('error: failed to upload file to %s (see log file for more)','updraftplus'), $ufile), 'error');
						$file_success = 0;
					}
				} else {
					$updraftplus->log('Dropbox error: '.$e->getMessage());
					$updraftplus->log('Dropbox '.sprintf(__('error: failed to upload file to %s (see log file for more)','updraftplus'), $ufile), 'error');
					$file_success = 0;
				}
			}
			if ($file_success) {
				$updraftplus->uploaded_file($file);
				$microtime_elapsed = microtime(true)-$microtime;
				$speedps = $filesize/$microtime_elapsed;
				$speed = sprintf("%.2d",$filesize)." Kb in ".sprintf("%.2d",$microtime_elapsed)."s (".sprintf("%.2d", $speedps)." Kb/s)";
				$updraftplus->log("Dropbox: File upload success (".$file."): $speed");
				$updraftplus->jobdata_delete('updraft_duido_'.$hash);
				$updraftplus->jobdata_delete('updraft_duidi_'.$hash);
			}

		}

		return null;

	}

	public static function defaults() {
		return array('Z3Q3ZmkwbnplNHA0Zzlx', 'bTY0bm9iNmY4eWhjODRt');
	}

	function delete($files) {

		global $updraftplus;
		if (is_string($files)) $files=array($files);

		if (UpdraftPlus_Options::get_updraft_option('updraft_dropboxtk_request_token', 'xyz') == 'xyz') {
			$updraftplus->log('You do not appear to be authenticated with Dropbox');
			$updraftplus->log(sprintf(__('You do not appear to be authenticated with %s (whilst deleting)', 'updraftplus'), 'Dropbox'), 'warning');
			return false;
		}

		try {
			$dropbox = $this->bootstrap();
		} catch (Exception $e) {
			$updraftplus->log('Dropbox error: '.$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
			$updraftplus->log(sprintf(__('Failed to access %s when deleting (see log file for more)', 'updraftplus'), 'Dropbox'), 'warning');
			return false;
		}

		foreach ($files as $file) {
			$ufile = apply_filters('updraftplus_dropbox_modpath', $file);
			$updraftplus->log("Dropbox: request deletion: $ufile");

			try {
				$dropbox->delete($ufile);
				$file_success = 1;
			} catch (Exception $e) {
				$updraftplus->log('Dropbox error: '.$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
			}

			if (isset($file_success)) {
				$updraftplus->log('Dropbox: delete succeeded');
			} else {
				return false;
			}
		}

	}

	function download($file) {

		global $updraftplus;

		if (UpdraftPlus_Options::get_updraft_option('updraft_dropboxtk_request_token', 'xyz') == 'xyz') {
			$updraftplus->log('You do not appear to be authenticated with Dropbox');
			$updraftplus->log(sprintf(__('You do not appear to be authenticated with %s','updraftplus'), 'Dropbox'), 'error');
			return false;
		}

		try {
			$dropbox = $this->bootstrap();
		} catch (Exception $e) {
			$updraftplus->log('Dropbox error: '.$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
			$updraftplus->log('Dropbox error: '.$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')', 'error');
			return false;
		}

		$updraft_dir = $updraftplus->backups_dir_location();
		$microtime = microtime(true);

		$try_the_other_one = false;

		$ufile = apply_filters('updraftplus_dropbox_modpath', $file);

		try {
			$get = $dropbox->getFile($ufile, $updraft_dir.'/'.$file, null, true);
		} catch (Exception $e) {
			// TODO: Remove this October 2013 (we stored in the wrong place for a while...)
			$try_the_other_one = true;
			$possible_error = $e->getMessage();
			$updraftplus->log('Dropbox error: '.$e);
		}

		// TODO: Remove this October 2013 (we stored files in the wrong place for a while...)
		if ($try_the_other_one) {
			$dropbox_folder = trailingslashit(UpdraftPlus_Options::get_updraft_option('updraft_dropbox_folder'));
			try {
				$get = $dropbox->getFile($dropbox_folder.'/'.$file, $updraft_dir.'/'.$file, null, true);
				if (isset($get['response']['body'])) {
					$updraftplus->log("Dropbox: downloaded ".round(strlen($get['response']['body'])/1024,1).' Kb');
				}
			}  catch (Exception $e) {
				$updraftplus->log($possible_error, 'error');
				$updraftplus->log($e->getMessage(), 'error');
			}
		}

	}

	public static function config_print() {

		?>
			<tr class="updraftplusmethod dropbox">
				<td></td>
				<td>
				<img alt="Dropbox logo" src="<?php echo UPDRAFTPLUS_URL.'/images/dropbox-logo.png' ?>">
				<p><em><?php printf(__('%s is a great choice, because UpdraftPlus supports chunked uploads - no matter how big your site is, UpdraftPlus can upload it a little at a time, and not get thwarted by timeouts.','updraftplus'),'Dropbox');?></em></p>
				</td>
			</tr>

			<tr class="updraftplusmethod dropbox">
			<th></th>
			<td>
			<?php
			// Check requirements.
			global $updraftplus_admin;
			if (!function_exists('mcrypt_encrypt')) {
				$updraftplus_admin->show_double_warning('<strong>'.__('Warning','updraftplus').':</strong> '. sprintf(__('Your web server\'s PHP installation does not included a required module (%s). Please contact your web hosting provider\'s support and ask for them to enable it.', 'updraftplus'), 'mcrypt'));
				/*
				.' '.sprintf(__("UpdraftPlus's %s module <strong>requires</strong> %s. Please do not file any support requests; there is no alternative.",'updraftplus'),'Dropbox', 'mcrypt'), 'dropbox')
				*/
			}
			$updraftplus_admin->curl_check('Dropbox', false, 'dropbox');
			?>
			</td>
			</tr>

			<?php

				$defmsg = '<tr class="updraftplusmethod dropbox"><td></td><td><strong>'.__('Need to use sub-folders?','updraftplus').'</strong> '.__('Backups are saved in','updraftplus').' apps/UpdraftPlus. '.__('If you back up several sites into the same Dropbox and want to organise with sub-folders, then ','updraftplus').'<a href="http://updraftplus.com/shop/">'.__("there's an add-on for that.",'updraftplus').'</a></td></tr>';

				echo apply_filters('updraftplus_dropbox_extra_config', $defmsg); ?>

			<tr class="updraftplusmethod dropbox">
				<th><?php _e('Authenticate with Dropbox','updraftplus');?>:</th>
				<td><p><?php if (UpdraftPlus_Options::get_updraft_option('updraft_dropboxtk_request_token','xyz') != 'xyz') echo "<strong>(You appear to be already authenticated).</strong>"; ?> <a href="?page=updraftplus&action=updraftmethod-dropbox-auth&updraftplus_dropboxauth=doit"><?php echo __('<strong>After</strong> you have saved your settings (by clicking \'Save Changes\' below), then come back here once and click this link to complete authentication with Dropbox.','updraftplus');?></a>
				</p>
				</td>
			</tr>

			<?php
			// Legacy: only show this next setting to old users who had a setting stored
			if (UpdraftPlus_Options::get_updraft_option('updraft_dropbox_appkey')) {
			?>

				<tr class="updraftplusmethod dropbox">
					<th>Your Dropbox App Key:</th>
					<td><input type="text" autocomplete="off" style="width:332px" id="updraft_dropbox_appkey" name="updraft_dropbox_appkey" value="<?php echo htmlspecialchars(UpdraftPlus_Options::get_updraft_option('updraft_dropbox_appkey')) ?>" /></td>
				</tr>
				<tr class="updraftplusmethod dropbox">
					<th>Your Dropbox App Secret:</th>
					<td><input type="text" style="width:332px" id="updraft_dropbox_secret" name="updraft_dropbox_secret" value="<?php echo htmlspecialchars(UpdraftPlus_Options::get_updraft_option('updraft_dropbox_secret')); ?>" /></td>
				</tr>

			<?php } ?>

		<?php
	}

	public static function action_auth() {
		if ( isset( $_GET['oauth_token'] ) ) {
			self::auth_token();
		} elseif (isset($_GET['updraftplus_dropboxauth'])) {
			// Clear out the existing credentials
			if ('doit' == $_GET['updraftplus_dropboxauth']) {
				UpdraftPlus_Options::update_updraft_option("updraft_dropboxtk_request_token",'');
				UpdraftPlus_Options::update_updraft_option("updraft_dropboxtk_access_token",'');
			}
			self::auth_request();
		}
	}

	public static function show_authed_admin_warning() {
		global $updraftplus_admin;

		$dropbox = self::bootstrap();
		$accountInfo = $dropbox->accountInfo();

		$message = "<strong>".__('Success','updraftplus').'</strong>: '.sprintf(__('you have authenticated your %s account','updraftplus'),'Dropbox');

		if ($accountInfo['code'] != "200") {
			$message .= " (".__('though part of the returned information was not as expected - your mileage may vary','updraftplus').")". $accountInfo['code'];
		} else {
			$body = $accountInfo['body'];
			$message .= ". <br>".sprintf(__('Your %s account name: %s','updraftplus'),'Dropbox', htmlspecialchars($body->display_name));

			try {
				$quota_info = $body->quota_info;
				$total_quota = max($quota_info->quota, 1);
				$normal_quota = $quota_info->normal;
				$shared_quota = $quota_info->shared;
				$available_quota =$total_quota - ($normal_quota + $shared_quota);
				$used_perc = round(($normal_quota + $shared_quota)*100/$total_quota, 1);
				$message .= ' <br>'.sprintf(__('Your %s quota usage: %s %% used, %s available','updraftplus'), 'Dropbox', $used_perc, round($available_quota/1048576, 1).' Mb');
			} catch (Exception $e) {
			}

		}
		$updraftplus_admin->show_admin_warning($message);

	}

	public static function auth_token() {
		$previous_token = UpdraftPlus_Options::get_updraft_option("updraft_dropboxtk_request_token","xyz");
		self::bootstrap();
		$new_token = UpdraftPlus_Options::get_updraft_option("updraft_dropboxtk_request_token","xyz");
		if ($new_token && $new_token != "xyz") {
			add_action('admin_notices', array('UpdraftPlus_BackupModule_dropbox', 'show_authed_admin_warning') );
		}
	}

	// Acquire single-use authorization code
	public static function auth_request() {
		self::bootstrap();
	}

	// This basically reproduces the relevant bits of bootstrap.php from the SDK
	public static function bootstrap() {

		require_once(UPDRAFTPLUS_DIR.'/includes/Dropbox/API.php'	);
		require_once(UPDRAFTPLUS_DIR.'/includes/Dropbox/Exception.php');
		require_once(UPDRAFTPLUS_DIR.'/includes/Dropbox/API.php');
		require_once(UPDRAFTPLUS_DIR.'/includes/Dropbox/OAuth/Consumer/ConsumerAbstract.php');
		require_once(UPDRAFTPLUS_DIR.'/includes/Dropbox/OAuth/Storage/StorageInterface.php');
		require_once(UPDRAFTPLUS_DIR.'/includes/Dropbox/OAuth/Storage/Encrypter.php');
		require_once(UPDRAFTPLUS_DIR.'/includes/Dropbox/OAuth/Storage/WordPress.php');
		require_once(UPDRAFTPLUS_DIR.'/includes/Dropbox/OAuth/Consumer/Curl.php');
//		require_once(UPDRAFTPLUS_DIR.'/includes/Dropbox/OAuth/Consumer/WordPress.php');

		$key = UpdraftPlus_Options::get_updraft_option('updraft_dropbox_secret');
		$sec = UpdraftPlus_Options::get_updraft_option('updraft_dropbox_appkey');

		// Set the callback URL
		$callback = admin_url('options-general.php?page=updraftplus&action=updraftmethod-dropbox-auth');

		// Instantiate the Encrypter and storage objects
		$encrypter = new Dropbox_Encrypter('ThisOneDoesNotMatterBeyondLength');

		// Instantiate the storage
		$storage = new Dropbox_WordPress($encrypter, "updraft_dropboxtk_");

//		WordPress consumer does not yet work
//		$OAuth = new Dropbox_ConsumerWordPress($sec, $key, $storage, $callback);

		// Get the DropBox API access details
		list($d2, $d1) = self::defaults();
		if (empty($sec)) { $sec = base64_decode($d1); }; if (empty($key)) { $key = base64_decode($d2); }

		try {
			$OAuth = new Dropbox_Curl($sec, $key, $storage, $callback);
		} catch (Exception $e) {
			global $updraftplus;
			$updraftplus->log("Dropbox Curl Error: ".$e->getMessage());
			$updraftplus->log("Dropbox Curl Error: ".$e->getMessage(), 'error');
			return false;
		}
		return new Dropbox_API($OAuth);
	}

}
