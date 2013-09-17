<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth consumer edit form block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /** Key used to store consumer data into the registry */
    const REGISTRY_KEY_CURRENT_CONSUMER = 'current_consumer';

    /** Keys used to retrieve values from subscription data array */
    const DATA_ENTITY_ID = 'entity_id';

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Edit_Form
     */
    protected function _prepareForm()
    {
        $consumerData = $this->_coreRegistry->registry(self::REGISTRY_KEY_CURRENT_CONSUMER);

        $form = $this->_formFactory->create(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save',
                    $consumerData[self::DATA_ENTITY_ID]
                        ? array('id' => $consumerData[self::DATA_ENTITY_ID]) : array()),
                'method' => 'post')
            );

        $fieldset = $form->addFieldset('consumer_fieldset', array(
            'legend' => __('Add-On Information'), 'class' => 'fieldset-wide'
        ));

        if ($consumerData[self::DATA_ENTITY_ID]) {
            $fieldset->addField(
                'id', 'hidden', array('name' => 'id', 'value' => $consumerData[self::DATA_ENTITY_ID]));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => __('Name'),
            'title'     => __('Name'),
            'required'  => true
        ));

        $fieldset->addField('key', 'text', array(
            'name'      => 'key',
            'label'     => __('Key'),
            'title'     => __('Key'),
            'disabled'  => true,
            'required'  => true
        ));

        $fieldset->addField('secret', 'text', array(
            'name'      => 'secret',
            'label'     => __('Secret'),
            'title'     => __('Secret'),
            'disabled'  => true,
            'required'  => true
        ));

        $fieldset->addField('callback_url', 'text', array(
            'name'      => 'callback_url',
            'label'     => __('Callback URL'),
            'title'     => __('Callback URL'),
            'required'  => false,
            'class'     => 'validate-url',
        ));

        $fieldset->addField('rejected_callback_url', 'text', array(
            'name'      => 'rejected_callback_url',
            'label'     => __('Rejected Callback URL'),
            'title'     => __('Rejected Callback URL'),
            'required'  => false,
            'class'     => 'validate-url',
        ));

        $fieldset->addField('http_post_url', 'text', array(
            'name'      => 'http_post_url',
            'label'     => __('Http Post URL'),
            'title'     => __('Http Post URL'),
            'required'  => true,
            'class'     => 'validate-url'
        ));

        $form->setUseContainer(true);
        $form->setValues($consumerData);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
