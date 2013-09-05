<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract Fieldset block for RMA view
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Abstract extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Form, created in parent block
     *
     * @var \Magento\Data\Form
     */
    protected $_parentForm = null;

    /**
     * Get Form Object Which is Parent to this block
     *
     * @return null|\Magento\Data\Form
     */
    public function getParentForm()
    {
        if (is_null($this->_parentForm) && $this->getParentBlock()) {
            $this->_parentForm = $this->getParentBlock()->getForm();
        }
        return $this->_parentForm;
    }

    /**
     * Add specific fieldset block to parent block form
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Absract
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_rma');
        $form = $this->getParentForm();

        $this->_addFieldset();

        if ($form && $model) {
            $form->setValues($model->getData());
        }
        if ($form) {
            $this->setForm($form);
        }

        return $this;
    }

    /**
     * Add fieldset with required fields
     */
    protected function _addFieldset()
    {
    }

    /**
     * Getter of model's data
     *
     * @param string $field
     * @return mixed
     */
    public function getRmaData($field)
    {
        $model = Mage::registry('current_rma');
        if ($model) {
            return $model->getData($field);
        } else {
            return null;
        }
    }

    /**
     * Get Order, RMA Attached to
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Get Customer Name (billing name)
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->escapeHtml($this->getOrder()->getCustomerName());
    }
}
