<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Pbridge result payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Block_Checkout_Payment_Result extends Magento_Core_Block_Template
{
    /**
     * Pbridge data
     *
     * @var Magento_Pbridge_Helper_Data
     */
    protected $_pbridgeData = null;

    /**
     * @param Magento_Pbridge_Helper_Data $pbridgeData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Pbridge_Helper_Data $pbridgeData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
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
