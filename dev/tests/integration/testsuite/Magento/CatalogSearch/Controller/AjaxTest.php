<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Controller;

class AjaxTest extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/query.php
     */
    public function testSuggestAction()
    {
        $this->getRequest()->setParam('q', 'query_text');
        $this->dispatch('catalogsearch/ajax/suggest');
        $this->assertContains('query_text', $this->getResponse()->getBody());
    }
}
