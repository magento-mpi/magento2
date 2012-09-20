<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Acl_Loader_Resource extends Mage_Core_Model_Acl_Loader_Resource_Abstract
{
    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_config = isset($data['config']) ? $data['config'] : Mage::getSingleton('Mage_Backend_Model_Acl_Config');
        $this->_objectFactory = isset($data['objectFactory']) ? $data['objectFactory'] : Mage::getConfig();
    }
}
