<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tab;

class General
    extends \Magento\Adminhtml\Block\Widget\Form
{
    /**
     * Return current gift registry type instance
     *
     * @return \Magento\GiftRegistry\Model\Type
     */
    public function getType()
    {
        return \Mage::registry('current_giftregistry_type');
    }

    /**
     * Prepares layout and set element renderer
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getLayout()->hasElement($this->getNameInLayout() . '_element')) {
            $this->getLayout()->unsetElement($this->getNameInLayout() . '_element');
        }
        \Magento\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                '\Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Form\Renderer\Element',
                $this->getNameInLayout() . '_element'
            )
        );
    }

    /**
     * Prepare general properties form
     *
     * @return \Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tab\General
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form();
        $form->setFieldNameSuffix('type');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'  => __('General Information')
        ));

        if ($this->getType()->getId()) {
            $fieldset->addField('type_id', 'hidden', array(
                'name' => 'type_id'
            ));
        }

        $fieldset->addField('code', 'text', array(
            'name'     => 'code',
            'label'    => __('Code'),
            'required' => true,
            'class'    => 'validate-code'
        ));

        $fieldset->addField('label', 'text', array(
            'name'     => 'label',
            'label'    => __('Label'),
            'required' => true,
            'scope'    => 'store'
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'     => 'sort_order',
            'label'    => __('Sort Order'),
            'scope'    => 'store'
        ));

        $fieldset->addField('is_listed', 'select', array(
            'label'    => __('Is Listed'),
            'name'     => 'is_listed',
            'values'   => \Mage::getSingleton('Magento\Backend\Model\Config\Source\Yesno')->toOptionArray(),
            'scope'    => 'store'
        ));

        $form->setValues($this->getType()->getData());
        $this->setForm($form);
        $form->setDataObject($this->getType());

        return parent::_prepareForm();
    }
}
