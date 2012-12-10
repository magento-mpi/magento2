<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mage_Backend_Block_System_Config_Form_Field object factory
 */
class Mage_Backend_Block_System_Config_Form_Field_Factory
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
     * @return Mage_Backend_Block_System_Config_Form_Field
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->get('Mage_Backend_Block_System_Config_Form_Field', $data);
    }
}
