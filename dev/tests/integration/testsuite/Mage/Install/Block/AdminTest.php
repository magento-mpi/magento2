<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Install_Block_AdminTest extends PHPUnit_Framework_TestCase
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

        /** @var $session Mage_Install_Model_Session */
        $session = Mage::getSingleton('Mage_Install_Model_Session');
        $session->setAdminData(array_merge($preserve, $omit));

        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout', array('area' => 'install'));
        /** @var $block Mage_Install_Block_Admin */
        $block = $layout->createBlock('Mage_Install_Block_Admin');
        $output = $block->toHtml();

        $this->assertEmpty($session->getAdminData());
        // form elements must be present with values
        foreach ($preserve as $key => $value) {
            $this->assertRegExp(sprintf('/<input[^>]+name="admin\[%s\]"[^>]+value="%s"/s', $key, $value), $output);
        }
        // form elements must be present without values
        foreach (array_keys($omit) as $key) {
            $this->assertRegExp(sprintf('/<input[^>]+name="admin\[%s\]"[^>]+>/s', $key), $output);
            $this->assertNotRegExp(sprintf('/<input[^>]+name="admin\[%s\]"[^>]+value="[^"]+"[^>]+>/s', $key), $output);
        }
    }
}
