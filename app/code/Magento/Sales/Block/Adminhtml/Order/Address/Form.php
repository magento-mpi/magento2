<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order edit address block
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Address;

class Form
    extends \Magento\Sales\Block\Adminhtml\Order\Create\Form\Address
{
    protected $_template = 'order/address/form.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\FormFactory $customerFormFactory
     * @param \Magento\Backend\Helper\Addresses $adminhtmlAddresses
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\FormFactory $customerFormFactory,
        \Magento\Backend\Helper\Addresses $adminhtmlAddresses,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $formFactory,
            $coreData,
            $jsonEncoder,
            $addressFactory,
            $customerFormFactory,
            $adminhtmlAddresses,
            $customerHelper,
            $data
        );
    }

    /**
     * Order address getter
     *
     * @return \Magento\Sales\Model\Order\Address
     */
    protected function _getAddress()
    {
        return $this->_coreRegistry->registry('order_address');
    }

    /**
     * Define form attributes (id, method, action)
     *
     * @return \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Address
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->_form->setId('edit_form');
        $this->_form->setMethod('post');
        $this->_form->setAction($this->getUrl('sales/*/addressSave', array('address_id'=>$this->_getAddress()->getId())));
        $this->_form->setUseContainer(true);
        return $this;
    }

    /**
     * Form header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Order Address Information');
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return $this->_getAddress()->getData();
    }
}
