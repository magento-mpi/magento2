<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Renderer for customer group ID
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Renderer\Attribute;

class Group
    extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    protected $_template = 'edit/tab/account/form/renderer/group.phtml';

    /**
     * Customer address
     *
     * @var \Magento\Customer\Helper\Address
     */
    protected $_customerAddress = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Helper\Address $customerAddress,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_customerAddress = $customerAddress;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve disable auto group change element HTML ID
     *
     * @return string
     */
    protected function _getDisableAutoGroupChangeElementHtmlId()
    {
        return $this->getDisableAutoGroupChangeAttribute()->getAttributeCode();
    }

    /**
     * Retrieve disable auto group change checkbox label text
     *
     * @return string
     */
    public function getDisableAutoGroupChangeCheckboxLabel()
    {
        return __($this->getDisableAutoGroupChangeAttribute()->getFrontendLabel());
    }

    /**
     * Retrieve disable auto group change checkbox state
     *
     * @return string
     */
    public function getDisableAutoGroupChangeCheckboxState()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        $checkedByDefault = ($customer && $customer->getId())
            ? false : $this->_customerAddress->getDisableAutoGroupAssignDefaultValue();

        $value = $this->getDisableAutoGroupChangeAttributeValue();
        $state = '';
        if (!empty($value) || $checkedByDefault) {
            $state = 'checked';
        }
        return $state;
    }

    /**
     * Retrieve disable auto group change checkbox element HTML NAME
     *
     * @return string
     */
    public function getDisableAutoGroupChangeCheckboxElementName()
    {
        return $this->getElement()->getForm()->getFieldNameSuffix()
            . '[' . $this->_getDisableAutoGroupChangeElementHtmlId() . ']';
    }

    /**
     * Retrieve disable auto group change checkbox element HTML ID
     *
     * @return string
     */
    public function getDisableAutoGroupChangeCheckboxElementId()
    {
        return $this->_getDisableAutoGroupChangeElementHtmlId();
    }
}
