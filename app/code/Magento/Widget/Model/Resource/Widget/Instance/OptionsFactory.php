<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Widget_Model_OptionsFactory
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
     * Create new action object
     *
     * @param $type
     * @param array $data
     * @return Magento_Core_Model_Option_ArrayInterface
     */
    public function create($type, array $data = array())
    {
        return $this->_objectManager->create($type, $data);
    }
}
