<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Adminhtml\Billing\Agreement\View\Tab;

/**
 * Adminhtml billing agreement related orders tab
 */
class Orders extends \Magento\View\Element\Text\ListText
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * Initialize grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('billing_agreement_orders');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Related Orders');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Related Orders');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
