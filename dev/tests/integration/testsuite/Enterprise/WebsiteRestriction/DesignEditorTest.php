<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_WebsiteRestriction
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test of visual design editor toolbar presence in "website restriction" page type
 */
class Enterprise_WebsiteRestriction_DesignEditorTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @magentoDataFixture Mage/Catalog/controllers/_files/products.php
     */
    public function testIndexStub()
    {
        $this->getRequest()->setParam('handle', 'restriction_index_stub');
        $this->dispatch('design/editor/page');
        $this->assertContains('id="vde_toolbar"', $this->getResponse()->getBody());
    }
}
