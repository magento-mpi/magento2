<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Cms\Controller\Page.
 */
namespace Magento\Cms\Controller;

class PageTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function testViewAction()
    {
        $this->dispatch('/about-magento-demo-store');
        $this->assertContains('About Magento Store', $this->getResponse()->getBody());
    }
}
