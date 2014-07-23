<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Url
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Resource\Url'
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/Model/Resource/_files/url_rewrites.php
     */
    public function testGetLastUsedRewriteRequestIncrement()
    {
        $this->markTestIncomplete('@TODO: UrlRewrite');
        $this->assertEquals(1000, $this->_model->getLastUsedRewriteRequestIncrement('url-key-', '.html', 1));
    }
}
