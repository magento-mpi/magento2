<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Tab;

class Giftregistry
    extends \Magento\Adminhtml\Block\Template
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    /**
     * Set identifier and title
     */
    protected  function _construct()
    {
        parent::_construct();
        $this->setId('gifregustry');
        $this->setTitle(__('Gift Registry'));
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
        $customer = \Mage::registry('current_customer');
        return $customer->getId()
           && \Mage::helper('Magento\GiftRegistry\Helper\Data')->isEnabled()
           && $this->_authorization->isAllowed('Magento_GiftRegistry::customer_magento_giftregistry');
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
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/giftregistry_customer/grid', array('_current' => true));
    }
}
