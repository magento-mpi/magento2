<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml shipping UPS content block
 */
namespace Magento\Backend\Block\System\Shipping;

class Ups extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Usa\Model\Shipping\Carrier\Ups
     */
    protected $_shippingModel;

    /**
     * @var \Magento\Core\Model\Website
     */
    protected $_websiteModel;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Usa\Model\Shipping\Carrier\Ups $shippingModel
     * @param \Magento\Core\Model\Website $websiteModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Usa\Model\Shipping\Carrier\Ups $shippingModel,
        \Magento\Core\Model\Website $websiteModel,
        array $data = array()
    ) {
        $this->_shippingModel = $shippingModel;
        $this->_websiteModel = $websiteModel;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Get shipping model
     *
     * @return \Magento\Usa\Model\Shipping\Carrier\Ups
     */
    public function getShippingModel()
    {
        return $this->_shippingModel;
    }

    /**
     * Get website model
     *
     * @return \Magento\Core\Model\Website
     */
    public function getWebsiteModel()
    {
        return $this->_websiteModel;
    }

    /**
     * Get store config
     *
     * @param string $path
     * @param mixed $store
     * @return mixed
     */
    public function getConfig($path, $store = null)
    {
        return $this->_storeConfig->getConfig($path, $store);
    }
}
