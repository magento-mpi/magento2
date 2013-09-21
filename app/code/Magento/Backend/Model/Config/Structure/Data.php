<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Backend_Model_Config_Structure_Data extends Magento_Config_Data
{
    /**
     * Merge additional config
     *
     * @param array $config
     */
    public function merge(array $config)
    {
        parent::merge($config['config']['system']);
    }
}
