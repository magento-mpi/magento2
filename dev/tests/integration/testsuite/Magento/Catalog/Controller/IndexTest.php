<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Catalog\Controller\Index.
 */
class Magento_Catalog_Controller_IndexTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    public function testIndexAction()
    {
        $this->dispatch('catalog/index');

        $this->assertRedirect();
    }
}
