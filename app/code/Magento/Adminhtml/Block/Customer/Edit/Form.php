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
 * Adminhtml customer edit form block
 */

namespace Magento\Adminhtml\Block\Customer\Edit;

class Form extends \Magento\Adminhtml\Block\Widget\Form
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Data\Form\Factory
     */
    protected $_formFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        parent::__construct($coreData, $context, $data);
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'edit_form',
                'action'    => $this->getData('action'),
                'method'    => 'post',
                'enctype'   => 'multipart/form-data',
            ))
        );

        $customer = $this->_coreRegistry->registry('current_customer');

        if ($customer->getId()) {
            $form->addField('entity_id', 'hidden', array(
                'name' => 'customer_id',
            ));
            $form->setValues($customer->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
