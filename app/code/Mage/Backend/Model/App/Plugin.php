<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_Backend_Model_App_Plugin
{
    public function beforeGetRequest($arguments)
    {
        echo 'plugin<br />';
        return $arguments;
    }
}
