<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Catalog_Controller_Index.
 */
class Mage_Catalog_Controller_IndexTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testIndexAction()
    {
        $this->dispatch('catalog/index');

        $this->assertRedirect();
    }
}
