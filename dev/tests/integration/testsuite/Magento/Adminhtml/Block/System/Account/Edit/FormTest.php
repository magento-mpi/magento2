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
namespace Magento\Adminhtml\Block\System\Account\Edit;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepareForm()
    {
        $user = \Mage::getModel('Magento\User\Model\User')
            ->loadByUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);

        /** @var $session \Magento\Backend\Model\Auth\Session */
        $session = \Mage::getSingleton('Magento\Backend\Model\Auth\Session');
        $session->setUser($user);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');

        /** @var \Magento\Adminhtml\Block\System\Account\Edit\Form */
        $block = $layout->createBlock('Magento\Adminhtml\Block\System\Account\Edit\Form');
        $block->toHtml();

        $form = $block->getForm();

        $this->assertInstanceOf('Magento\Data\Form', $form);
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
            $this->assertInstanceOf('Magento\Data\Form\Element\AbstractElement', $element);
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
