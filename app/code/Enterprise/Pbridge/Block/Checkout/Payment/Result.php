<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Pbridge result payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Checkout_Payment_Result extends Magento_Core_Block_Template
{
    /**
     * Pbridge data
     *
     * @var Enterprise_Pbridge_Helper_Data
     */
    protected $_pbridgeData = null;

    /**
     * @param Enterprise_Pbridge_Helper_Data $pbridgeData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Pbridge_Helper_Data $pbridgeData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Return JSON array of Payment Bridge incoming data
     *
     * @return string
     */
    public function getJsonHiddenPbridgeParams()
    {
        return $this->_coreData->jsonEncode(
            $this->_pbridgeData->getPbridgeParams()
        );
    }
}
