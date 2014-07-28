<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

/**
 * Test class for \Magento\Catalog\Model\Url.
 *
 * @magentoDataFixture Magento/Catalog/_files/url_rewrites.php
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Url
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Url'
        );
    }

    /**
     * Retrieve loaded url rewrite
     *
     * @param string $idPath
     * @return \Magento\UrlRewrite\Model\UrlRewrite
     */
    protected function _loadRewrite($idPath)
    {
        /** @var $rewrite \Magento\UrlRewrite\Model\UrlRewrite */
        $rewrite = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\UrlRewrite\Model\UrlRewrite'
        );
        $rewrite->loadByIdPath($idPath);
        return $rewrite;
    }

    public function testGetStores()
    {
        $stores = $this->_model->getStores();
        $this->assertArrayHasKey(1, $stores); /* Current store identifier */
    }

    public function testGetResource()
    {
        $resource = $this->_model->getResource();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Url', $resource);
        $this->assertSame($resource, $this->_model->getResource());
    }
}
