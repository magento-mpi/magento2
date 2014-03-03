<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Store\Delete;

/**
 * Adminhtml cms block edit form
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('store_delete_form');
        $this->setTitle(__('Block Information'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $dataObject = $this->getDataObject();

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'data' => array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ))
        );

        $form->setHtmlIdPrefix('store_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Backup Options'), 'class' => 'fieldset-wide'));

        $fieldset->addField('item_id', 'hidden', array(
            'name'  => 'item_id',
            'value' => $dataObject->getId(),
        ));

        $fieldset->addField('create_backup', 'select', array(
            'label'     => __('Create DB Backup'),
            'title'     => __('Create DB Backup'),
            'name'      => 'create_backup',
            'options'   => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
            'value'     => '1',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
