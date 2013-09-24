<?php
/**
 * Pdf config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Order_Pdf_Config
{
    /** @var Magento_Sales_Model_Order_Pdf_Config_Data */
    protected $_dataStorage;

    /**
     * @param Magento_Sales_Model_Order_Pdf_Config_Data $dataStorage
     */
    public function __construct(Magento_Sales_Model_Order_Pdf_Config_Data $dataStorage)
    {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Get renderer configuration data by type
     *
     * @param string $type
     * @return array
     */
    public function getRendererData($type)
    {
        return $this->_dataStorage->get("renderers/$type", array());
    }

    /**
     * Get list of total lines
     *
     * @return array
     */
    public function getTotals()
    {
        return $this->_dataStorage->get('totals', array());
    }
}
