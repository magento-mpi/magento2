<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Integration
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Integration\Edit\Tab;

use \Magento\Integration\Controller\Adminhtml\Integration;

/**
 * Main Integration info edit form
 *
 * @category   Magento
 * @package    Magento_Integration
 */
class Info extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**#@+
     * Form elements names.
     */
    const HTML_ID_PREFIX = 'integration_properties_';
    const DATA_ID = 'integration_id';
    const DATA_NAME = 'name';
    const DATA_EMAIL = 'email';
    const DATA_ENDPOINT = 'endpoint';
    const DATA_SETUP_TYPE = 'setup_type';
    /**#@-*/

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $registry, $formFactory, $data);
    }

    /**
     * Set form id prefix, declare fields for integration info
     *
     * @return \Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix(self::HTML_ID_PREFIX);
        $integrationData = $this->_coreRegistry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION);
        $this->_addGeneralFieldset($form, $integrationData);
        $this->_addDetailsFieldset($form, $integrationData);
        $form->setValues($integrationData);
        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Integration Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Add fieldset with general integration information.
     *
     * @param \Magento\Data\Form $form
     * @param array $integrationData
     */
    protected function _addGeneralFieldset($form, $integrationData)
    {
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('General')));
        if (isset($integrationData[self::DATA_ID])) {
            $fieldset->addField(self::DATA_ID, 'hidden', array('name' => 'id'));
        }
        $fieldset->addField(
            self::DATA_NAME,
            'text',
            array(
                'label' => __('Name'),
                'name' => self::DATA_NAME,
                'required' => true,
                'disabled' => false,
                'maxlength' => '255'
            )
        );
        $fieldset->addField(
            self::DATA_EMAIL,
            'text',
            array(
                'label' => __('Email'),
                'name' => self::DATA_EMAIL,
                'disabled' => false,
                'class' => 'validate-email',
                'maxlength' => '254'
            )
        );
        $fieldset->addField(
            self::DATA_ENDPOINT,
            'text',
            array(
                'label' => __('Callback URL'),
                'name' => self::DATA_ENDPOINT,
                'disabled' => false,
                // @codingStandardsIgnoreStart
                'note' => __(
                    'When using Oauth for token exchange, enter URL where Oauth credentials can be POST-ed. We strongly recommend you to use https://'
                )
                // @codingStandardsIgnoreEnd
            )
        );
    }

    /**
     * Add fieldset with integration details. This fieldset is available for existing integrations only.
     *
     * @param \Magento\Data\Form $form
     * @param array $integrationData
     */
    protected function _addDetailsFieldset($form, $integrationData)
    {
        if (isset($integrationData[self::DATA_ID])) {
            $fieldset = $form->addFieldset('details_fieldset', array('legend' => __('Integration Details')));
            /** @var \Magento\Integration\Block\Adminhtml\Integration\Tokens $tokensBlock */
            $tokensBlock = $this->getChildBlock('integration_tokens');
            foreach ($tokensBlock->getFormFields() as $field) {
                $fieldset->addField($field['name'], $field['type'], $field['metadata']);
            }
        }
    }
}
