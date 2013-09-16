<?php
/**
 * Magento_Webhook_Block_AdminHtml_Subscription_Edit_Form
 *
 * @magentoAppArea adminhtml
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Block_Adminhtml_Subscription_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareForm()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Core_Model_Layout');

        /** @var Magento_Core_Model_Registry $registry */
        $registry = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Registry');
        $subscription = array(
            'name' => 'subscriptionName',
            'endpoint_url' => 'example.url.com',
            'format' => 'JSON',
            'authentication_type' => 'manual',
            'topics' => 'customer/created',
            'subscription_id' => '4'
        );
        $registry
            ->register(
                'current_subscription',
                $subscription
            );

        /** @var Magento_Webhook_Block_Adminhtml_Subscription_Edit_Form $block */
        $block = $layout->createBlock('Magento_Webhook_Block_Adminhtml_Subscription_Edit_Form',
            '', array('registry' => $registry)
        );
        $block->toHtml();

        $form = $block->getForm();

        $this->assertInstanceOf('Magento_Data_Form', $form);
        $this->assertEquals('post', $form->getData('method'));
        $this->assertEquals('edit_form', $form->getId());
        $this->assertTrue($form->getUseContainer());

        $expectedFieldset = array(
            'name' => array(
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'value' => $subscription['name']
            ),
            'endpoint_url' => array(
                'name' => 'endpoint_url',
                'type' => 'text',
                'required' => true,
                'value' => $subscription['endpoint_url']
            ),
            'format' => array(
                'name' => 'format',
                'type' => 'select',
                'required' => false,
                'value' => $subscription['format']
            ),
            'authentication_type' => array(
                'name' => 'authentication_type',
                'type' => 'select',
                'required' => false,
                'value' => $subscription['authentication_type']
            ),
            'topics' => array(
                'name' => 'topics[]',
                'type' => 'select',
                'required' => true,
                'value' => $subscription['topics']
            ),
        );

        foreach ($expectedFieldset as $fieldId => $field) {
            $element = $form->getElement($fieldId);
            $this->assertInstanceOf('Magento_Data_Form_Element_Abstract', $element);
            $this->assertEquals($field['name'], $element->getName(), 'Wrong \'' . $fieldId . '\' field name');
            $this->assertEquals($field['type'], $element->getType(), 'Wrong \'' . $fieldId . ' field type');
            $this->assertEquals(
                $field['required'],
                $element->getData('required'),
                'Wrong \'' . $fieldId . '\' requirement state'
            );
            if (array_key_exists('value', $field)) {
                $this->assertEquals($field['value'], $element->getData('value'), 'Wrong \'' . $fieldId . '\' value');
            }
        }
    }
}
