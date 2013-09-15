<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * OAuth Consumer Edit Block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Oauth\Block\Adminhtml\Oauth\Consumer;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Consumer model
     *
     * @var \Magento\Oauth\Model\Consumer
     */
    protected $_model;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get consumer model
     *
     * @return \Magento\Oauth\Model\Consumer
     */
    public function getModel()
    {
        if (null === $this->_model) {
            $this->_model = $this->_coreRegistry->registry('current_consumer');
        }
        return $this->_model;
    }

    /**
     * Construct edit page
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'Magento_Oauth';
        $this->_controller = 'adminhtml_oauth_consumer';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label'     => __('Save and Continue Edit'),
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

        if (!$this->getModel()
            || !$this->getModel()->getId()
            || !$this->_authorization->isAllowed('Magento_Oauth::consumer_delete')
        ) {
            $this->_removeButton('delete');
        }
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getModel()->getId()) {
            return __('Edit Consumer');
        } else {
            return __('New Consumer');
        }
    }
}
