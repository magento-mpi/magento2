<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Integration
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Integration;

use \Magento\Integration\Controller\Adminhtml\Integration;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

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
        $this->_registry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Initialize Integration edit page. Set management buttons
     *
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_integration';
        $this->_blockGroup = 'Magento_Integration';
        parent::_construct();
    }

    /**
     * Get current loaded integration ID
     *
     */
    public function getBannerId()
    {
        return $this->_registry->registry(
            Integration::REGISTRY_KEY_CURRENT_INTEGRATION
        )[Integration::DATA_INTEGRATION_ID];
    }

    /**
     * Get header text for edit page
     *
     */
    public function getHeaderText()
    {
        if ($this->_registry
            ->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION)[Integration::DATA_INTEGRATION_ID]
        ) {
            return $this->escapeHtml(
                $this->_registry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION)->getName()
            );
        } else {
            return __('New Integration');
        }
    }

    /**
     * Get form action URL
     *
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
