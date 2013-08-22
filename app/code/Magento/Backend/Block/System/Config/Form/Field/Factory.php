<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_Backend_Block_System_Config_Form_Field object factory
 */
class Magento_Backend_Block_System_Config_Form_Field_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new config object
     *
     * @param array $data
     * @return Magento_Backend_Block_System_Config_Form_Field
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento_Backend_Block_System_Config_Form_Field', $data);
    }
}
