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
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
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
