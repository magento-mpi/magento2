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

        $fieldset = $form->addFieldset('base_fieldset', array(
                    'legend'    =>  __('Integration Tokens for Extensions'),
                    'class'    =>  'fieldset-wide'
                ));

        $fieldset->addField('token', 'text', array(
            'label'     => __('Token'),
            'name'      => 'token',
            'readonly'  => true
        ));

        $fieldset->addField('token-secret', 'text', array(
            'label'     => __('Token Secret'),
            'name'      => 'token-secret',
            'readonly'  => true
        ));

        $fieldset->addField('client-id', 'text', array(
            'label'     => __('Client ID'),
            'name'      => 'client-id',
            'readonly'  => true
        ));

        $fieldset->addField('client-secret', 'text', array(
            'label'     => __('Client Secret'),
            'name'      => 'client-secret',
            'readonly'  => true
        ));

        // TODO: retrieve token associated to this integration to populate the form
        // $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
