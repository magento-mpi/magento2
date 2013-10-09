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
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General;

class AbstractGeneral extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Form, created in parent block
     *
     * @var \Magento\Data\Form
     */
    protected $_parentForm = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

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
     * @return \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\AbstractGeneral
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_rma');
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
        $model = $this->_coreRegistry->registry('current_rma');
        if ($model) {
            return $model->getData($field);
        } else {
            return null;
        }
    }

    /**
     * Get Order, RMA Attached to
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
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
