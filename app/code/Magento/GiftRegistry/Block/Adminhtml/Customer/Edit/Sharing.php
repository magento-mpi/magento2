<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Customer_Edit_Sharing
    extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $systemStore;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_System_Store $systemStore
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_System_Store $systemStore,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);

        $this->systemStore = $systemStore;
        $this->storeManager = $storeManager;
    }

    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getActionUrl(),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Sharing Information'),
            'class'  => 'fieldset-wide'
        ));

        $fieldset->addField('emails', 'text', array(
            'label'    => __('Emails'),
            'required' => true,
            'class'    => 'validate-emails',
            'name'     => 'emails',
            'note'     => 'Enter list of emails, comma-separated.'
        ));

        if (!$this->storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'select', array(
                'label'    => __('Send From'),
                'required' => true,
                'name'     => 'store_id',
                'values'   => $this->systemStore->getStoreValuesForForm()
            ));
        }

        $fieldset->addField('message', 'textarea', array(
            'label' => __('Message'),
            'name'  => 'message',
            'style' => 'height: 50px;',
            'after_element_html' => $this->getShareButton()
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setDataObject();

        return parent::_prepareForm();
    }

    /**
     * Return sharing form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/share', array('_current' => true));
    }

    /**
     * Create button
     *
     * @return string
     */
    public function getShareButton()
    {
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->addData(array(
                'id'      => '',
                'label'   => __('Share Gift Registry'),
                'type'    => 'submit'
            ))->toHtml();
    }
}
