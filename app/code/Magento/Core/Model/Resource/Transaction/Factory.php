<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_Transaction_Factory
{
    /**
     * Global factory
     *
     * @var Magento_Core_Model_Config
     */
    protected $_objectFactory;

    public function __construct(array $data = array())
    {
        $this->_objectFactory = isset($data['objectFactory']) ? $data['objectFactory'] : Mage::getConfig();
    }

    public function create()
    {
        return $this->_objectFactory->getModelInstance('Magento_Core_Model_Resource_Transaction');
    }
}
