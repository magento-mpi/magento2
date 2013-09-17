<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Element_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create form element with provided params
     *
     * @param string $className
     * @param array $data
     * @return Magento_Data_Form_Element_Abstract
     */
    public function create($className, array $data = array())
    {
        return $this->_objectManager->create($className, array('attributes' => $data));
    }
}
