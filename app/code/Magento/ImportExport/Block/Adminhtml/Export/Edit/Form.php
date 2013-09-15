<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export edit form block
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ImportExport\Block\Adminhtml\Export\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form before rendering HTML.
     *
     * @return \Magento\ImportExport\Block\Adminhtml\Export\Edit\Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'     => 'edit_form',
                'action' => $this->getUrl('*/*/getFilter'),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Export Settings')));
        /** @var $entitySourceModel \Magento\ImportExport\Model\Source\Export\Entity */
        $entitySourceModel = \Mage::getModel('Magento\ImportExport\Model\Source\Export\Entity');
        $fieldset->addField('entity', 'select', array(
            'name'     => 'entity',
            'title'    => __('Entity Type'),
            'label'    => __('Entity Type'),
            'required' => false,
            'onchange' => 'varienExport.getFilter();',
            'values'   => $entitySourceModel->toOptionArray()
        ));
        /** @var $formatSourceModel \Magento\ImportExport\Model\Source\Export\Format */
        $formatSourceModel = \Mage::getModel('Magento\ImportExport\Model\Source\Export\Format');
        $fieldset->addField('file_format', 'select', array(
            'name'     => 'file_format',
            'title'    => __('Export File Format'),
            'label'    => __('Export File Format'),
            'required' => false,
            'values'   => $formatSourceModel->toOptionArray()
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
