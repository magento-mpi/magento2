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
 * Rating edit form
 */

namespace Magento\Adminhtml\Block\Rating;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
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

    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_controller = 'rating';

        $this->_updateButton('save', 'label', __('Save Rating'));
        $this->_updateButton('delete', 'label', __('Delete Rating'));

        if ($this->getRequest()->getParam($this->_objectId)) {
            $ratingData = \Mage::getModel('Magento\Rating\Model\Rating')
                ->load($this->getRequest()->getParam($this->_objectId));

            $this->_coreRegistry->register('rating_data', $ratingData);
        }
    }

    public function getHeaderText()
    {
        $ratingData = $this->_coreRegistry->registry('rating_data');
        if ($ratingData && $ratingData->getId()) {
            return __("Edit Rating #%1", $this->escapeHtml($ratingData->getRatingCode()));
        } else {
            return __('New Rating');
        }
    }
}
