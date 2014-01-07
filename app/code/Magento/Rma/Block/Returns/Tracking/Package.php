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
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Rma\Helper\Data $rmaData,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setPackageInfo($this->_registry->registry('rma_package_shipping'));
    }

    /**
     * Get packages of RMA
     *
     * @return array
     */
    public function getPackages()
    {
        return $this->getPackageInfo()->getPackages();
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
        $carrierCode = $this->getPackageInfo()->getCarrierCode();
        $carrier = $this->_rmaData->getCarrier($carrierCode, $this->_storeManager->getStore()->getId());
        if ($carrier) {
            $containerTypes = $carrier->getContainerTypes();
            $containerType = !empty($containerTypes[$code]) ? $containerTypes[$code] : '';
            return $containerType;
        }
        return '';
    }
}
