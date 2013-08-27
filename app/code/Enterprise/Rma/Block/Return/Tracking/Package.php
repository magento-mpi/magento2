<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Return_Tracking_Package extends Magento_Shipping_Block_Tracking_Popup
{
    /**
     * Rma data
     *
     * @var Enterprise_Rma_Helper_Data
     */
    protected $_rmaData = null;

    /**
     * @param Enterprise_Rma_Helper_Data $rmaData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Rma_Helper_Data $rmaData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setPackageInfo(Mage::registry('rma_package_shipping'));
    }

    /**
     * Get packages of RMA
     *
     * @return array
     */
    public function getPackages()
    {
        return unserialize($this->getPackageInfo()->getPackages());
    }

    /**
     * Print package url for creating pdf
     *
     * @return string
     */
    public function getPrintPackageUrl()
    {
        $data['hash'] = $this->getRequest()->getParam('hash');
        return $this->getUrl('*/*/packageprint', $data);
    }

    /**
     * Return name of container type by its code
     *
     * @param string $code
     * @return string
     */
    public function getContainerTypeByCode($code)
    {
        $carrierCode= $this->getPackageInfo()->getCarrierCode();
        $carrier    = $this->_rmaData->getCarrier($carrierCode, Mage::app()->getStore()->getId());
        if ($carrier) {
            $containerTypes = $carrier->getContainerTypes();
            $containerType = !empty($containerTypes[$code]) ? $containerTypes[$code] : '';
            return $containerType;
        }
        return '';
    }
}
