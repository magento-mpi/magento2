<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin customer left menu
 */
namespace Magento\Adminhtml\Block\Customer\Edit;

class Tabs extends \Magento\Adminhtml\Block\Widget\Tabs
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Customer Information'));
    }

    protected function _beforeToHtml()
    {
        \Magento\Profiler::start('customer/tabs');

        $this->addTab('account', array(
            'label'     => __('Account Information'),
            'content'   => $this->getLayout()
                ->createBlock('Magento\Adminhtml\Block\Customer\Edit\Tab\Account')->initForm()->toHtml(),
            'active'    => $this->_coreRegistry->registry('current_customer')->getId() ? false : true
        ));

        $this->addTab('addresses', array(
            'label'     => __('Addresses'),
            'content'   => $this->getLayout()
                ->createBlock('Magento\Adminhtml\Block\Customer\Edit\Tab\Addresses')->initForm()->toHtml(),
        ));


        // load: Orders, Shopping Cart, Wishlist, Product Reviews, Product Tags - with ajax

        if ($this->_coreRegistry->registry('current_customer')->getId()) {

            if ($this->_authorization->isAllowed('Magento_Sales::actions_view')) {
                $this->addTab('orders', array(
                    'label'     => __('Orders'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/orders', array('_current' => true)),
                 ));
            }

            $this->addTab('cart', array(
                'label'     => __('Shopping Cart'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/carts', array('_current' => true)),
            ));

            $this->addTab('wishlist', array(
                'label'     => __('Wishlist'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/wishlist', array('_current' => true)),
            ));

            if ($this->_authorization->isAllowed('Magento_Newsletter::subscriber')) {
                $this->addTab('newsletter', array(
                    'label'     => __('Newsletter'),
                    'content'   => $this->getLayout()
                        ->createBlock('Magento\Adminhtml\Block\Customer\Edit\Tab\Newsletter')->initForm()->toHtml()
                ));
            }

            if ($this->_authorization->isAllowed('Magento_Review::reviews_all')) {
                $this->addTab('reviews', array(
                    'label'     => __('Product Reviews'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/productReviews', array('_current' => true)),
                ));
            }
        }

        $this->_updateActiveTab();
        \Magento\Profiler::stop('customer/tabs');
        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if ($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}
