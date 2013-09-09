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
 * Adminhtml store edit form
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
abstract class Magento_Adminhtml_Block_System_Store_Edit_FormAbstract extends Magento_Backend_Block_Widget_Form_Generic
{

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($formFactory, $coreData, $context, $data);
    }

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
     * return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'edit_form',
                'action'    => $this->getData('action'),
                'method'    => 'post',
            ))
        );

        $this->_prepareStoreFieldSet($form);

        $form->addField('store_type', 'hidden', array(
            'name'      => 'store_type',
            'no_span'   => true,
            'value'     => Mage::registry('store_type')
        ));

        $form->addField('store_action', 'hidden', array(
            'name'      => 'store_action',
            'no_span'   => true,
            'value'     => Mage::registry('store_action')
        ));

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        $this->_eventManager->dispatch('adminhtml_store_edit_form_prepare_form', array('block' => $this));

        return parent::_prepareForm();
    }

    /**
     * Build store type specific fieldset
     *
     * @abstract
     * @param Magento_Data_Form $form
     */
    abstract protected function _prepareStoreFieldset(Magento_Data_Form $form);
}
