<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Block\System\Store\Edit;

/**
 * Adminhtml store edit form
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
abstract class AbstractForm extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('coreStoreForm');
    }

    /**
     * Prepare form data
     *
     * return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'data' => array(
                'id'        => 'edit_form',
                'action'    => $this->getData('action'),
                'method'    => 'post',
            ))
        );

        $this->_prepareStoreFieldSet($form);

        $form->addField('store_type', 'hidden', array(
            'name'      => 'store_type',
            'no_span'   => true,
            'value'     => $this->_coreRegistry->registry('store_type')
        ));

        $form->addField('store_action', 'hidden', array(
            'name'      => 'store_action',
            'no_span'   => true,
            'value'     => $this->_coreRegistry->registry('store_action')
        ));

        $form->setAction($this->getUrl('adminhtml/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        $this->_eventManager->dispatch('adminhtml_store_edit_form_prepare_form', array('block' => $this));

        return parent::_prepareForm();
    }

    /**
     * Build store type specific fieldset
     *
     * @abstract
     * @param \Magento\Data\Form $form
     */
    abstract protected function _prepareStoreFieldset(\Magento\Data\Form $form);
}
