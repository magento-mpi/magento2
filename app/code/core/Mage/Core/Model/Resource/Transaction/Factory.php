<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_Transaction_Factory
{
    /**
     * Global factory
     *
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    public function __construct(array $data = array())
    {
        $this->_objectFactory = isset($data['objectFactory']) ? $data['objectFactory'] : Mage::getConfig();
    }

    public function create()
    {
        return $this->_objectFactory->getModelInstance('Mage_Core_Model_Resource_Transaction');
    }
}
