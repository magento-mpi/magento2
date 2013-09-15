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
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Main;

class Formgroup extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('set_fieldset', array('legend'=>__('Add New Group')));

        $fieldset->addField('attribute_group_name', 'text',
                            array(
                                'label' => __('Name'),
                                'name' => 'attribute_group_name',
                                'required' => true,
                            )
        );

        $fieldset->addField('submit', 'note',
                            array(
                                'text' => $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')
                                            ->setData(array(
                                                'label'     => __('Add Group'),
                                                'onclick'   => 'this.form.submit();',
                                                                                                'class' => 'add'
                                            ))
                                            ->toHtml(),
                            )
        );

        $fieldset->addField('attribute_set_id', 'hidden',
                            array(
                                'name' => 'attribute_set_id',
                                'value' => $this->_getSetId(),
                            )

        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/catalog_product_group/save'));
        $this->setForm($form);
    }

    protected function _getSetId()
    {
        return ( intval($this->getRequest()->getParam('id')) > 0 )
                    ? intval($this->getRequest()->getParam('id'))
                    : Mage::getModel('Magento\Eav\Model\Entity\Type')
                        ->load($this->_coreRegistry->registry('entityType'))
                        ->getDefaultAttributeSetId();
    }
}
