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

class ResultTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/query.php
     */
    public function testIndexActionTranslation()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $layout = $objectManager->get('Magento\View\LayoutInterface');
        $layout->setIsCacheable(false);
        $objectManager->get('Magento\Locale\ResolverInterface')->setLocale('de_DE');
        $layout = $objectManager->get('Magento\View\LayoutInterface');
        $layout->setIsCacheable(false);

        $this->getRequest()->setParam('q', 'query_text');
        $this->dispatch('catalogsearch/result');

        $responseBody = $this->getResponse()->getBody();
        $this->assertNotContains('for="search">Search', $responseBody);
        $this->assertContains('Ihre Suche ergab keine Ergebnisse', $responseBody);
        $this->assertNotContains('Search entire store here...', $responseBody);
        $this->assertContains('Erweiterte Suche', $responseBody);
    }

    public function testIndexActionXSSQueryVerification()
    {
        $this->getRequest()->setParam('q', '<script>alert(1)</script>');
        $this->dispatch('catalogsearch/result');

        $responseBody = $this->getResponse()->getBody();
        $data = '<script>alert(1)</script>';
        $this->assertNotContains($data, $responseBody);
        $this->assertContains(htmlspecialchars($data, ENT_COMPAT, 'UTF-8', false), $responseBody);
    }
}
