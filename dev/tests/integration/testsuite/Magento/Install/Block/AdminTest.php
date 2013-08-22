<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Install_Block_AdminTest extends PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        $preserve = array(
            'username' => 'admin',
            'email' => 'admin@example.com',
            'firstname' => 'First',
            'lastname' => 'Last',
        );
        $omit = array(
            'password' => 'password_with_1_number',
            'password_confirmation' => 'password_with_1_number',
        );

        /** @var $session Magento_Core_Model_Session_Generic */
        $session = Mage::getSingleton('Magento_Install_Model_Session');
        $session->setAdminData(array_merge($preserve, $omit));

        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout', array('area' => 'install'));
        /** @var $block Magento_Install_Block_Admin */
        $block = $layout->createBlock('Magento_Install_Block_Admin');
        $output = $block->toHtml();

        $this->assertEmpty($session->getAdminData());
        // form elements must be present with values
        foreach ($preserve as $key => $value) {
            $this->assertSelectCount(sprintf('input[name=admin[%s]][value=%s]', $key, $value), 1, $output);
        }
        // form elements must be present without values
        foreach ($omit as $key => $value) {
            $this->assertSelectCount(sprintf('input[name=admin[%s]]', $key), 1, $output);
            $this->assertSelectCount(sprintf('input[name=admin[%s]][value=%s]', $key, $value), 0, $output);
        }
    }
}
