<?php
/**
 * Adminhtml store edit
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backend_Block_Adminhtml_System_Store_Edit extends Magento_Adminhtml_Block_System_Store_Edit
{
    /**
     * Store type 'store'
     */
    const STORE_TYPE_STORE = 'store';

    /**
     * @var Mage_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * Init class
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_updateDeleteButton();
    }

    /**
     * Update delete button
     */
    protected function _updateDeleteButton()
    {
        if (self::STORE_TYPE_STORE == $this->_registry->registry('store_type')) {
            $title = 'Store View';
            $url = $this->getUrl('*/*/deleteStorePost', array('item_id' => $this->getRequest()->getParam('store_id')));
            // @codingStandardsIgnoreStart
            $message = $this->__('Deleting a %1$s will not delete the information associated with the %1$s (e.g. categories, products, etc.), but the %1$s will not be able to be restored.', $title);
            // @codingStandardsIgnoreEnd
            $message .= ' ' . $this->__('Are you sure you want to do this?');
            $message = $this->quoteEscape($message, true);

            $this->_updateButton('delete', 'onclick', "deleteConfirm('{$message}', '{$url}')");
        }
    }
}
