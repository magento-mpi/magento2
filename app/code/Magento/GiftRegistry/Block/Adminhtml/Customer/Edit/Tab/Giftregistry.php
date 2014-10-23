<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Tab;

class Giftregistry extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct($context, $data);
    }

    /**
     * Set identifier and title
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gifregustry');
        $this->setTitle(__('Gift Registry'));
    }

    public function isAjaxLoaded()
    {
        return true;
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getTitle();
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTitle();
    }

    /**
     * Check whether tab can be showed
     *
     * @return bool
     */
    public function canShowTab()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        return $customer->getId() && $this->_giftRegistryData->isEnabled() && $this->_authorization->isAllowed(
            'Magento_GiftRegistry::customer_magento_giftregistry'
        );
    }

    /**
     * Check whether tab should be hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Precessor tab ID getter
     *
     * @return string
     */
    public function getAfter()
    {
        return 'reviews';
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Tab URL getter
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('adminhtml/giftregistry_customer/grid', array('_current' => true));
    }
}
