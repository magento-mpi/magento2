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
namespace Magento\Oauth\Block\Adminhtml\Oauth\Consumer;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /** Key used to store subscription data into the registry */
    const REGISTRY_KEY_CURRENT_CONSUMER = 'current_consumer';

    /** Keys used to retrieve values from consumer data array */
    const DATA_ENTITY_ID = 'entity_id';

    /** @var array $_consumerData */
    protected $_consumerData;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
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
