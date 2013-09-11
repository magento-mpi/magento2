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
 * User-attributes block for RMA Item  in Admin RMA edit
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit;

class Item extends \Magento\Adminhtml\Block\Widget\Form
{
    /**
     * Preparing form - container, which contains all attributes
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Edit\Item
     */
    public function initForm()
    {
        $form = new \Magento\Data\Form();
        $form->setHtmlIdPrefix('_rma');
        $form->setFieldNameSuffix();

        $item = \Mage::registry('current_rma_item');

        if (!$item->getId()) {
            // for creating RMA process when we have no item loaded, $item is just empty model
            $this->_populateItemWithProductData($item);
        }

        /* @var $customerForm \Magento\Customer\Model\Form */
        $customerForm = \Mage::getModel('\Magento\Rma\Model\Item\Form');
        $customerForm->setEntity($item)
            ->setFormCode('default')
            ->initDefaultValues();

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>__('RMA Item Details'))
        );

        $fieldset->setProductName($this->escapeHtml($item->getProductAdminName()));
        $okButton = $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button')
            ->setData(array(
                'label'   => __('OK'),
                'class'   => 'ok_button',
            ));
        $fieldset->setOkButton($okButton->toHtml());

        $cancelButton = $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button')
            ->setData(array(
                'label'   => __('Cancel'),
                'class'   => 'cancel_button',
            ));
        $fieldset->setCancelButton($cancelButton->toHtml());


        $attributes = $customerForm->getUserAttributes();

        foreach ($attributes as $attribute) {
            $attribute->unsIsVisible();
        }
        $this->_setFieldset($attributes, $fieldset);

        $form->setValues($item->getData());
        $this->setForm($form);
        return $this;
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changin layout
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _prepareLayout()
    {
        \Magento\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock(
                '\Magento\Adminhtml\Block\Widget\Form\Renderer\Element',
                $this->getNameInLayout() . '_element'
            )
        );
        \Magento\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                '\Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Renderer\Fieldset',
                $this->getNameInLayout() . '_fieldset'
            )
        );
        \Magento\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                '\Magento\Adminhtml\Block\Widget\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );

        return $this;
    }

    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'text' => '\Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Form\Element\Text',
            'textarea' => '\Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Form\Element\Textarea',
            'image' => '\Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Form\Element\Image',
        );
    }

    /**
     * Add needed data (Product name) to RMA item during create process
     *
     * @param \Magento\Rma\Model\Item $item
     */
    protected function _populateItemWithProductData($item)
    {
        if ($this->getProductId()) {
            $orderItem = \Mage::getModel('\Magento\Sales\Model\Order\Item')->load($this->getProductId());
            if ($orderItem && $orderItem->getId()) {
                $item->setProductAdminName(\Mage::helper('Magento\Rma\Helper\Data')->getAdminProductName($orderItem));
            }
        }
    }
}
