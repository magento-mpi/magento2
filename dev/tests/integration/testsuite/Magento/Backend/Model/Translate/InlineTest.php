<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Translate;

/**
 * Test class for \Magento\Backend\Model\Translate\Inline.
 *
 * @magentoAppArea adminhtml
 */
class InlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Translate\InlineInterface
     */
    protected $_translate;

    protected function setUp()
    {
        $this->_translate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Translate\InlineInterface');
    }

    /**
     * @magentoAdminConfigFixture dev/translate_inline/active_admin 1
     * @covers \Magento\Backend\Model\Translate\Inline::_getAjaxUrl
     */
    public function testAjaxUrl()
    {
        $body = '<html><body>some body</body></html>';
        /** @var \Magento\Backend\Model\UrlInterface $url */
        $url = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\UrlInterface');
        $url->getUrl(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE . '/ajax/translate');
        $this->_translate->processResponseBody($body, true);
        $this->assertContains(
            $url->getUrl(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE . '/ajax/translate'),
            $body
        );
    }
}
