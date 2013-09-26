<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Order_Pdf_Total_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Default total model
     *
     * @var string
     */
    protected $_defaultTotalModel = 'Magento_Sales_Model_Order_Pdf_Total_Default';

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create instance of a total model
     *
     * @param string|null $class
     * @param array $arguments
     * @return Magento_Sales_Model_Order_Pdf_Total_Default
     * @throws Magento_Core_Exception
     */
    public function create($class = null, $arguments = array())
    {
        $class = $class ?: $this->_defaultTotalModel;
        if (!is_a($class, 'Magento_Sales_Model_Order_Pdf_Total_Default', true)) {
            throw new Magento_Core_Exception(
                __("The PDF total model {$class} must be or extend Magento_Sales_Model_Order_Pdf_Total_Default.")
            );
        }
        return $this->_objectManager->create($class, $arguments);
    }
}
