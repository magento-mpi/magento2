<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Page;

use Mtf\Factory\Factory;

/**
 * Class BackendPage
 *
 * @package Mtf\Page
 */
 class BackendPage extends Page
 {
     /**
      * Init page. Set page url
      */
     protected function _init()
     {
         $this->_url = $_ENV['app_backend_url'] . static::MCA;
     }

     /**
      * Open backend page and log in if needed
      *
      * @param array $params
      * @return $this
      */
     public function open(array $params = [])
     {
         Factory::getApp()->magentoBackendLoginUser();
         return parent::open($params);
     }
 }
