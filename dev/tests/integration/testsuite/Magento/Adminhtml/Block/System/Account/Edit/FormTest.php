<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_System_Account_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareForm()
    {
        $user = Mage::getModel('Magento_User_Model_User')->loadByUsername(Magento_TestFramework_Bootstrap::ADMIN_NAME);

        /** @var $session Magento_Backend_Model_Auth_Session */
        $session = Mage::getSingleton('Magento_Backend_Model_Auth_Session');
        $session->setUser($user);

        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout');

        /** @var Magento_Adminhtml_Block_System_Account_Edit_Form */
        $block = $layout->createBlock('Magento_Adminhtml_Block_System_Account_Edit_Form');
        $block->toHtml();

        $form = $block->getForm();

        $this->assertInstanceOf('Magento_Data_Form', $form);
        $this->assertEquals('post', $form->getData('method'));
        $this->assertEquals($block->getUrl('*/system_account/save'), $form->getData('action'));
        $this->assertEquals('edit_form', $form->getId());
        $this->assertTrue($form->getUseContainer());

        $expectedFieldset = array(
            'username' => array(
                'name' => 'username',
                'type' => 'text',
                'required' => true,
                'value' => $user->getData('username')
            ),
            'firstname' => array(
                'name' => 'firstname',
                'type' => 'text',
                'required' => true,
                'value' => $user->getData('firstname')
            ),
            'lastname' => array(
                'name' => 'lastname',
                'type' => 'text',
                'required' => true,
                'value' => $user->getData('lastname')
            ),
            'email' => array(
                'name' => 'email',
                'type' => 'text',
                'required' => true,
                'value' => $user->getData('email')
            ),
            'password' => array(
                'name' => 'password',
                'type' => 'password',
                'required' => false
            ),
            'confirmation' => array(
                'name' => 'password_confirmation',
                'type' => 'password',
                'required' => false
            ),
            'interface_locale' => array(
                'name' => 'interface_locale',
                'type' => 'select',
                'required' => false
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
