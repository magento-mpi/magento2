<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class config
 *
 * @category   Magento
 * @package    Magento_Connect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Maged_Model_Config extends Maged_Model_Config_Abstract
{
    /**
     * Get channel config class
     * @return Maged_Model_Config_Interface
     */
    public function getChannelConfig()
    {
        $this->load();
        $channel = trim($this->get('root_channel'));
        if (!empty($channel)) {
            try {
                return $this->controller()->model('config_'.$channel, true);
            } catch (Exception $e) {
                throw new Exception('Not valid config.ini file.');
            }
        } else {
            throw new Exception('Not valid config.ini file.');
        }
    }

    /**
    * Save post data to config
    *
    * @param array $p
    * @return Maged_Model_Config
    */
    public function saveConfigPost($p)
    {
        $configParams = array(
            'protocol',
            'preferred_state',
            'use_custom_permissions_mode',
            'mkdir_mode',
            'chmod_file_mode',
            'magento_root',
            'downloader_path',
            'root_channel_uri',
            'root_channel',
            'ftp',
        );
        foreach ($configParams as $paramName){
            if (isset($p[$paramName])) {
               $this->set($paramName, $p[$paramName]);
            }
        }
        $this->save();
        return $this;
    }
}
