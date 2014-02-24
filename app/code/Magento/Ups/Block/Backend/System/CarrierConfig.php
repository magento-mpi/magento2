<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ups\Block\Backend\System;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context as TemplateContext;
use Magento\Ups\Helper\Config as ConfigHelper;
use Magento\Core\Model\Website;

/**
 * Backend shipping UPS content block
 */
class CarrierConfig extends Template
{
    /**
     * Shipping carrier config
     *
     * @var \Magento\Ups\Helper\Config
     */
    protected $carrierConfig;

    /**
     * @var \Magento\Core\Model\Website
     */
    protected $_websiteModel;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Ups\Helper\Config $carrierConfig
     * @param \Magento\Core\Model\Website $websiteModel
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        ConfigHelper $carrierConfig,
        Website $websiteModel,
        array $data = array()
    ) {
        $this->carrierConfig = $carrierConfig;
        $this->_websiteModel = $websiteModel;
        parent::__construct($context, $data);
    }

    /**
     * Get shipping model
     *
     * @return \Magento\Ups\Helper\Config
     */
    public function getCarrierConfig()
    {
        return $this->carrierConfig;
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
