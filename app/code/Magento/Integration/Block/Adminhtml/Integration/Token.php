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

/**
 * Main Integration properties edit form
 *
 * @category   Magento
 * @package    Magento_Integration
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Token extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Set form id prefix, declare fields for integration consumer modal
     *
     * @return \Magento\Integration\Block\Adminhtml\Integration\Token
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'integration_token_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

//        $model = $this->_coreRegistry->registry('current_integration');

        $fieldset = $form->addFieldset('base_fieldset', array(
                    'legend'    =>  __('Access Tokens'),
                    'class'    =>  'fieldset-wide'
                ));
/*
        if ($model->getIntegrationId()) {
            $fieldset->addField('integration_id', 'hidden', array(
                'name' => 'integration_id',
            ));
        }
*/
        $fieldset->addField('token', 'text', array(
            'label'     => __('Token'),
            'name'      => 'token',
            'required'  => true,
            'disabled'  => true
        ));

        $fieldset->addField('token-secret', 'text', array(
            'label'     => __('Token Secret'),
            'name'      => 'token-secret',
            'required'  => true,
            'disabled'  => true
        ));

        $fieldset->addField('client-id', 'text', array(
            'label'     => __('Client ID'),
            'name'      => 'client-id',
            'required'  => true,
            'disabled'  => true
        ));

        $fieldset->addField('client-secret', 'text', array(
            'label'     => __('Client Secret'),
            'name'      => 'client-secret',
            'required'  => true,
            'disabled'  => true
        ));

//        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
