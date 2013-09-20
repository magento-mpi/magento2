<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Block_Status_Grid_Column_State extends Magento_Backend_Block_Widget_Grid_Column
{
    /**
     * @var Magento_Sales_Model_Order_Config
     */
    protected $_config;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Sales_Model_Order_Config $config
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Sales_Model_Order_Config $config,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);

        $this->_config = $config;
    }

    /**
     * Add decorated status to column
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return array($this, 'decorateState');
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param Magento_Sales_Model_Order_Status $row
     * @param Magento_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return string
     */
    public function decorateState($value, $row, $column, $isExport)
    {
        if ($value) {
            $cell = $value . '[' . $this->_config->getStateLabel($value) . ']';
        } else {
            $cell = $value;
        }
        return $cell;
    }
}
