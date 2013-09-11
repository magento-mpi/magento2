<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer account Store Credit tab
 *
 */
namespace Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab;

class Customerbalance
    extends \Magento\Adminhtml\Block\Widget
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    /**
     * Set identifier and title
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customerbalance');
        $this->setTitle(__('Store Credit'));
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
        return (bool)$customer->getId();
    }

    /**
     * Check whether tab should be hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        if( !$this->getRequest()->getParam('id') ) {
            return true;
        }
        return false;
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
     * Check whether content should be generated
     *
     * @return bool
     */
    public function getSkipGenerateContent()
    {
        return true;
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
     * Tab URL getter
     *
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/customerbalance/form', array('_current' => true));
    }
}
