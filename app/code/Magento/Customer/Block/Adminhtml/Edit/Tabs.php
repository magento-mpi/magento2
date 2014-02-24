<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit;

/**
 * Admin customer left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Customer Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        \Magento\Profiler::start('customer/tabs');

        $this->addTab('account', array(
            'label'     => __('Account Information'),
            'content'   => $this->getLayout()
                ->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\Account')->initForm()->toHtml(),
            'active'    => $this->_coreRegistry->registry('current_customer')->getId() ? false : true
        ));

        $this->addTab('addresses', array(
            'label'     => __('Addresses'),
            'content'   => $this->getLayout()
                ->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\Addresses')->initForm()->toHtml(),
        ));


        // load: Orders, Shopping Cart, Wishlist, Product Reviews, Product Tags - with ajax

        if ($this->_coreRegistry->registry('current_customer')->getId()) {

            if ($this->_authorization->isAllowed('Magento_Sales::actions_view')) {
                $this->addTab('orders', array(
                    'label'     => __('Orders'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('customer/*/orders', array('_current' => true)),
                 ));
            }

            $this->addTab('cart', array(
                'label'     => __('Shopping Cart'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('customer/*/carts', array('_current' => true)),
            ));

            $this->addTab('wishlist', array(
                'label'     => __('Wishlist'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('customer/*/wishlist', array('_current' => true)),
            ));

            if ($this->_authorization->isAllowed('Magento_Newsletter::subscriber')) {
                $this->addTab('newsletter', array(
                    'label'     => __('Newsletter'),
                    'content'   => $this->getLayout()
                        ->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter')->initForm()->toHtml()
                ));
            }

            if ($this->_authorization->isAllowed('Magento_Review::reviews_all')) {
                $this->addTab('reviews', array(
                    'label'     => __('Product Reviews'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('customer/*/productReviews', array('_current' => true)),
                ));
            }
        }

        $this->_updateActiveTab();
        \Magento\Profiler::stop('customer/tabs');
        return parent::_beforeToHtml();
    }

    /**
     * @return void
     */
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
