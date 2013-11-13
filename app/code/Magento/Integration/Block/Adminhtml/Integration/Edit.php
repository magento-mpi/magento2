<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Integration;

use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;
use Magento\Integration\Controller\Adminhtml\Integration;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

    /**
     * Initialize dependencies.
     *
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
     * Initialize Integration edit page
     *
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_integration';
        $this->_blockGroup = 'Magento_Integration';
        parent::_construct();
    }

    /**
     * Get header text for edit page.
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (isset($this->_registry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION)[Info::DATA_ID])) {
            return __(
                "Edit Integration '%1'",
                $this->escapeHtml(
                    $this->_registry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION)[Info::DATA_NAME]
                )
            );
        } else {
            return __('New Integration');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
