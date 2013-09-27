<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth Consumer Edit Block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Edit extends Magento_Backend_Block_Widget_Form_Container
{
    /** Key used to store subscription data into the registry */
    const REGISTRY_KEY_CURRENT_CONSUMER = 'current_consumer';

    /** Keys used to retrieve values from consumer data array */
    const DATA_ENTITY_ID = 'entity_id';

    /** @var array $_consumerData */
    protected $_consumerData;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Registry $registry,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_consumerData = $registry->registry(self::REGISTRY_KEY_CURRENT_CONSUMER);
        if (!$this->_consumerData
            || !$this->_consumerData[self::DATA_ENTITY_ID]
            || !$this->_authorization->isAllowed('Magento_Oauth::consumer_delete')
        ) {
            $this->_removeButton('delete');
        }
    }

    /**
     * Construct edit page
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magento_Oauth';
        $this->_controller = 'adminhtml_oauth_consumer';

        $this->_addButton('save_and_continue_edit', array(
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 100);

        $this->_updateButton('save', 'label', __('Save'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', __('Delete'));
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_consumerData[self::DATA_ENTITY_ID]) {
            return __('Edit Add-On');
        } else {
            return __('New Add-On');
        }
    }
}
