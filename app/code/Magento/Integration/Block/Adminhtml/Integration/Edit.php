<?php
/**
 * Integration edit container.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Integration;

use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;
use Magento\Integration\Controller\Adminhtml\Integration;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

    /** @var \Magento\Integration\Helper\Data */
    protected $_integrationHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Integration\Helper\Data $integrationHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Integration\Helper\Data $integrationHelper,
        array $data = array()
    ) {
        $this->_registry = $registry;
        $this->_integrationHelper = $integrationHelper;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Integration edit page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_integration';
        $this->_blockGroup = 'Magento_Integration';
        parent::_construct();
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        if ($this->_integrationHelper->isConfigType(
            $this->_registry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION))
        ) {
            $this->_removeButton('save');
        }

        if ($this->_isNewIntegration()) {
            $this->removeButton('save')->addButton(
                'save',
                [
                    'id' => 'save-split-button',
                    'label' => __('Save'),
                    'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
                    'button_class' => 'PrimarySplitButton',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'save', 'target' => '#edit_form'],
                        ],
                    ],
                    'options' => [
                        'save_activate' => [
                            'id' => 'activate',
                            'label' => __('Save & Activate'),
                            'data_attribute' => [
                                'mage-init' => [
                                    'button' => [
                                        'event' => 'saveAndActivate',
                                        'target' => '#edit_form',
                                    ],
                                    'integration' => [
                                        'gridUrl' => $this->getUrl('*/*/'),
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
        }
    }

    /**
     * Get header text for edit page.
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_isNewIntegration()) {
            return __('New Integration');
        } else {
            return __(
                "Edit Integration '%1'",
                $this->escapeHtml(
                    $this->_registry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION)[Info::DATA_NAME]
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Determine whether we create new integration or editing an existing one.
     *
     * @return bool
     */
    protected function _isNewIntegration()
    {
        return !isset($this->_registry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION)[Info::DATA_ID]);
    }
}
