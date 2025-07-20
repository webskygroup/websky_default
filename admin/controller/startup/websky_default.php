<?php

namespace Opencart\Admin\Controller\Extension\WebskyDefault\Startup;

class WebskyDefault extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {
        if(!defined('WEBSKY_ROUTE_SEPARATOR')){
            if (version_compare(VERSION, '4.0.2.0', '>=')) {
                define('WEBSKY_ROUTE_SEPARATOR', '.');
            } else {
                define('WEBSKY_ROUTE_SEPARATOR', '|');
            }
            
        }
        if ($this->config->get('theme_websky_default_status')) {

          $this->event->register('view/common/header/before', new \Opencart\System\Engine\Action('extension/websky_default/startup/websky_default' . WEBSKY_ROUTE_SEPARATOR . 'view_common_header'));

        }

    }

    public function view_common_header(string &$route, array &$args): void
    {

        //print_r($args);
        if ($this->language->get('direction') == 'rtl') {
            $args["bootstrap"] = "../extension/websky_default/admin/view/stylesheet/bootstrap.rtl.min.css";
            $args["stylesheet"] = "../extension/websky_default/admin/view/stylesheet/stylesheet-rtl.css";

        }
         //print_r($args);

    }

   

}