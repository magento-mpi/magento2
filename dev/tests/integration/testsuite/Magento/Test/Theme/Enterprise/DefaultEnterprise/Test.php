<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     theme
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Theme\Enterprise\DefaultEnterprise;

/**
 * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
 */
class Test extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    /**
     * Assert that a page content contains references to both jQuery and jQzoom JavaScript libraries
     *
     * @param string $content
     */
    protected function _assertContainsJqZoom($content)
    {
        $this->assertContains('http://localhost/pub/lib/jquery/jquery.js', $content);
        $this->assertContains('/pub/lib/jquery/jqzoom/js/jquery.jqzoom-core-pack.js', $content);
        $this->assertContains('/pub/lib/jquery/jqzoom/css/jquery.jqzoom.css', $content);
    }

    /**
     * @magentoConfigFixture frontend/design/theme/full_name magento_fixed_width
     * @magentoConfigFixture current_store dev/js/merge_files 0
     * @magentoConfigFixture current_store dev/js/minify_files 0
     */
    public function testCatalogProductView()
    {
        $this->dispatch('catalog/product/view/id/1');
        $this->_assertContainsJqZoom($this->getResponse()->getBody());
    }

    /**
     * @magentoConfigFixture frontend/design/theme/full_name magento_fixed_width
     * @magentoConfigFixture current_store dev/js/merge_files 0
     * @magentoConfigFixture current_store dev/js/minify_files 0
     */
    public function testReviewProductList()
    {
        $this->dispatch('review/product/list/id/1');
        $this->_assertContainsJqZoom($this->getResponse()->getBody());
    }
}
