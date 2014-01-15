<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class SearchTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDataFixture Magento/Search/Controller/Adminhtml/_files/search_term_laptop.php
     * @magentoDataFixture Magento/Search/Controller/Adminhtml/_files/search_term_calculator.php
     */
    public function testRelatedGridAction()
    {
        $filter = base64_encode('search_query=lap');
        $this->getRequest()->setParam('filter', $filter);

        $this->dispatch('backend/catalog/search/relatedGrid');
        $responseText = $this->getResponse()->getBody();

        $this->assertNotContains('<body', $responseText, 'Ajax response should not contain <body>', true);
        $this->assertContains('Laptop', $responseText, 'Response does not contain the matched item');
        $this->assertNotContains('Calculator', $responseText, 'Response must contain only matched items');
    }
}
