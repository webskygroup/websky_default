<?php

namespace Opencart\Admin\Controller\Extension\WebskyDefault\Theme;

class WebskyDefault extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {

        $this->load->language('extension/websky_default/theme/websky_default');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme'),
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/websky_default/theme/websky_default', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id']),
        ];

        $data['save'] = $this->url->link('extension/websky_default/theme/websky_default|save', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id']);
        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme');

        if (isset($this->request->get['store_id'])) {
            $this->load->model('setting/setting');

            $setting_info = $this->model_setting_setting->getSetting('theme_websky_default', $this->request->get['store_id']);
        }

        if (isset($setting_info['theme_websky_default_status'])) {
            $data['theme_websky_default_status'] = $setting_info['theme_websky_default_status'];
        } else {
            $data['theme_websky_default_status'] = '';
        }
        $data['user_token'] = $this->session->data['user_token'];
        	$data['current_version'] = "1.0.5";
		$data['upgrade'] = false;

	  $url = 'https://opencart-ir.com/version/index.php?route=extension/websky_lastversion/module/websky_lastversion';
       $feilds=array(
            'extension_name'=>'websky_default'
           );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $feilds);
        // Execute post
        $json = curl_exec($ch);
     //   print_r($json);
        if ($json === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        $response_info=json_decode($json, true);
		if ($response_info) {
			$data['latest_version'] = $response_info['version_ext'];
			$data['date_added'] =($this->language->get('code') == 'fa') ? jdate($this->config->get('language_traditional_persian_shamsidate_format'), strtotime($response_info["date_added"])) : $response_info["date_added"];
			if (!version_compare($data['current_version'], $response_info['version_ext'], '>=')) {
				$data['upgrade'] = true;
			}
		} else {
			$data['latest_version'] = '';
			$data['date_added'] = '';
			$data['log'] = '';
		}
      

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/websky_default/theme/websky_default', $data));
    }

    public function save(): void
    {
        $this->load->language('extension/websky_default/theme/websky_default');

        $json = [];

        if (!$this->user->hasPermission('modify', 'extension/websky_default/theme/websky_default')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $this->load->model('setting/setting');

            $this->model_setting_setting->editSetting('theme_websky_default', $this->request->post, $this->request->get['store_id']);

            $json['success'] = $this->language->get('text_success');
        }
        
                $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function install(): void
    {
        $this->load->model('setting/startup');
        $startup_data = [
            'code' => 'theme_websky_default_catalog',
			'description' => 'theme_websky_default_catalog',
            'action' => 'catalog/extension/websky_default/startup/websky_default',
            'status' => 1,
            'sort_order' => 3,
        ];
        $this->model_setting_startup->deleteStartupByCode('theme_websky_default_catalog');
        $this->model_setting_startup->addStartup($startup_data);

        $startup_data_admin = [
            'code' => 'theme_websky_default',
			'description' => 'theme_websky_default_',
            'action' => 'admin/extension/websky_default/startup/websky_default',
            'status' => 1,
            'sort_order' => 4,
        ];
        $this->model_setting_startup->deleteStartupByCode('theme_websky_default');
        $this->model_setting_startup->addStartup($startup_data_admin);
    }

	 
	public function download(): void {
		$this->load->language('marketplace/marketplace');

		$json = [];

			if (isset($this->request->get['extension_name'])) {
			$extension_name = $this->request->get['extension_name'];
		} else {
			$json['error']= 'extension name null';
		}
		

	
		if (!$this->user->hasPermission('modify', 'marketplace/marketplace')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
		    


		    	$handle = fopen(DIR_STORAGE . 'marketplace/'.$extension_name.'.ocmod.zip' , 'w');

					$download = $this->get_data('https://opencart-ir.com/dl/'.$extension_name.'.ocmod.zip');

					fwrite($handle, $download);

					fclose($handle);
					
					$this->load->language('marketplace/installer');

	     	$json = [];

		if (!$this->user->hasPermission('modify', 'marketplace/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/extension');

			$file = DIR_STORAGE . 'marketplace/' . $extension_name . '.ocmod.zip';

			if (!is_file($file)) {
				$json['error'] = sprintf($this->language->get('error_file'), $extension_name . '.ocmod.zip');
			}


		if (!$json) {
		  
			// Unzip the files
			$zip = new \ZipArchive();

			if ($zip->open($file)) {
				$total = $zip->numFiles;
				$limit = 200;

				$start = 0;
				$end = $start > ($total - $limit) ? $total : ($start + $limit);

				// Check if any of the files already exist.
				for ($i = $start; $i < $end; $i++) {
					$source = $zip->getNameIndex($i);

					$destination = str_replace('\\', '/', $source);

					// Only extract the contents of the upload folder
					$path = $extension_name . '/' . $destination;
					$base = DIR_EXTENSION;
					$prefix = '';

					// image > image
					if (substr($destination, 0, 6) == 'image/') {
						$path = $destination;
						$base = substr(DIR_IMAGE, 0, -6);
					}

					// We need to store the path differently for vendor folders.
					if (substr($destination, 0, 15) == 'system/storage/') {
						$path = substr($destination, 15);
						$base = DIR_STORAGE;
						$prefix = 'system/storage/';
					}

					// Must not have a path before files and directories can be moved
					$path_new = '';

					$directories = explode('/', dirname($path));

					foreach ($directories as $directory) {
						if (!$path_new) {
							$path_new = $directory;
						} else {
							$path_new = $path_new . '/' . $directory;
						}

						// To fix storage location
						if (!is_dir($base . $path_new . '/') && mkdir($base . $path_new . '/', 0777)) {
						    echo 2;
						//	$this->model_setting_extension->addPath($extension_install_id, $prefix . $path_new);
						}
					}
				//	echo $base . $path;

					// If check if the path is not directory and check there is no existing file
					if (substr($source, -1) != '/') {
					   // echo 'zip://' . $file . '#' . $source, $base . $path;
					    copy('zip://' . $file . '#' . $source, $base . $path);
						if (!is_file($base . $path) && copy('zip://' . $file . '#' . $source, $base . $path)) {
						    echo 1;
						//	$this->model_setting_extension->addPath($extension_install_id, $prefix . $path);
						}
					}
				}

				$zip->close();
				$json['success'] = $this->language->get('text_success');
				$json['text'] = $this->language->get('text_install');

		    	$json['next'] = $this->url->link('extension/websky_default/theme/websky_default.update', 'user_token=' . $this->session->data['user_token'] . '&extension_name=websky_default', true);
		
					
			//	$this->model_setting_extension->editStatus($extension_install_id, 1);
			} else {
				$json['error'] = $this->language->get('error_unzip');
			}
		}

	
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
		public function update(): void {
		$this->load->language('marketplace/marketplace');

		$json = [];

			if (isset($this->request->get['extension_name'])) {
			$extension_name = $this->request->get['extension_name'];
		} else {
			$json['error']= 'extension name null';
		}
		

	
		if (!$this->user->hasPermission('modify', 'marketplace/marketplace')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
		    
	     $json['success']= 'extension name null';
		
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	private	function get_data($url)
{
  $ch = curl_init();
  $timeout = 15;
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
	

   
}
