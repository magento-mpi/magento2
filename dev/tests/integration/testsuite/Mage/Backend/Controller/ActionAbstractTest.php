<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Controller_ActionAbstract.
 *
 */
class Mage_Backend_Controller_ActionAbstractTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * Check redirection to startup page for logged user
     * @magentoConfigFixture admin/routers/adminhtml/args/frontName admin
     * @magentoConfigFixture current_store admin/security/use_form_key 1
     */
    public function testPreDispatch()
    {
        $expected = Mage::getSingleton('Mage_Backend_Model_Url')->getUrl('adminhtml/dashboard');
        $this->dispatch('/admin');
        try {
            foreach ($this->getResponse()->getHeaders() as $header) {
                if ($header['name'] == 'Location') {
                    $this->assertStringStartsWith($expected, $header['value'], 'Incorrect startup page url');
                    throw new Exception('Correct');
                }
            }
            $this->fail('There is no redirection to startup page');
        } catch (Exception $e) {
            $this->assertEquals('Correct', $e->getMessage());
            $this->assertRedirect();
        }
    }
}
