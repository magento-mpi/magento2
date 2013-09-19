<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Returns\Tracking;

class Package extends \Magento\Shipping\Block\Tracking\Popup
{
    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        parent::__construct($coreData, $context, $registry, $data);
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setPackageInfo($this->_coreRegistry->registry('rma_package_shipping'));
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
        $carrier    = $this->_rmaData->getCarrier($carrierCode, \Mage::app()->getStore()->getId());
        if ($carrier) {
            $containerTypes = $carrier->getContainerTypes();
            $containerType = !empty($containerTypes[$code]) ? $containerTypes[$code] : '';
            return $containerType;
        }
        return '';
    }
}
