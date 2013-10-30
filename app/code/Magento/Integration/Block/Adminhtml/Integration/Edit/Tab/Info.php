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
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Info extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Construct
     *
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
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
        $htmlIdPrefix = 'integration_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $integrationData = $this->_coreRegistry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION);
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Integration')));
        if (isset($integrationData[Integration::DATA_INTEGRATION_ID])) {
            $fieldset->addField(Integration::DATA_INTEGRATION_ID, 'hidden', array('name' => 'id'));
        }
        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => __('Integration Name'),
                'name' => 'name',
                'required' => true,
                'disabled' => false,
                'maxlength' => '255'
            )
        );
        $fieldset->addField(
            'email',
            'text',
            array(
                'label' => __('Email'),
                'name' => 'email',
                'required' => true,
                'disabled' => false,
                'class' => 'validate-email',
                'maxlength' => '254',
            )
        );
        $fieldset->addField(
            'authentication',
            'select',
            array(
                'label' => __('Authentication'),
                'name' => 'authentication',
                'disabled' => false,
                'options' => array(
                    \Magento\Integration\Model\Integration::AUTHENTICATION_OAUTH => __('OAuth'),
                    \Magento\Integration\Model\Integration::AUTHENTICATION_MANUAL => __('Manual'),
                ),
            )
        );
        $fieldset->addField(
            'endpoint',
            'text',
            array('label' => __('Endpoint URL'), 'name' => 'endpoint', 'required' => true, 'disabled' => false)
        );
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
     * Get additional script for tabs block
     *
     * @return string
     */
    protected function _toHtml()
    {
        $oauth = \Magento\Integration\Model\Integration::AUTHENTICATION_OAUTH;
        $script = <<<HTML
jQuery(function ($) {
    var jqAuthSel = $('#integration_properties_authentication'),
        jqEndpoint = $('#integration_properties_endpoint'),
        authFunction = function () {
            var isOauth = jqAuthSel.val() === '$oauth'
            $('.field-endpoint').children().toggle(isOauth);
            jqEndpoint.toggleClass('required-entry', isOauth);
        }
    authFunction();
    jqAuthSel.on('change', authFunction);
    $('form').on('submit', function () {
        if (jqAuthSel.val() !== '$oauth') {
            $('#integration_properties_endpoint').val('');
        }
    });
});
HTML;
        return parent::_toHtml() . sprintf('<script type="text/javascript">%s</script>', $script);
    }
}
