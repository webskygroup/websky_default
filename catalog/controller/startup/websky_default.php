<?php

namespace Opencart\Catalog\Controller\Extension\WebskyDefault\Startup;

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
        if ($this->config->get('theme_websky_default_status') and $this->config->get('config_theme')=='websky_default') {
            
            $this->event->register('view/*/before', new \Opencart\System\Engine\Action('extension/websky_default/startup/websky_default'.WEBSKY_ROUTE_SEPARATOR.'event'));
        }
    }

    public function event(string &$route, array &$args, mixed &$output): void
    {
       
        $override = [
            
            'common/header',
             
        ];
       
        if (in_array($route, $override)) {
           
            $route = 'extension/websky_default/' . $route;
        }
    }

   
}
