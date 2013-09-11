<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Cms\Controller\Page.
 */
class Magento_Cms_Controller_PageTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    public function testViewAction()
    {
        $this->dispatch('/about-magento-demo-store');
        $this->assertContains('About Magento Store', $this->getResponse()->getBody());
    }
}
