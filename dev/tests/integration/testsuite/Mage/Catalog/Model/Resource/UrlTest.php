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

class Mage_Catalog_Model_Resource_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Resource_Url
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Catalog_Model_Resource_Url();
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @magentoDataFixture Mage/Catalog/Model/Resource/_files/url_rewrites.php
     */
    public function testGetLastUsedRewriteRequestIncrement()
    {
        $this->markTestIncomplete('Bug MAGETWO-724');
        $this->assertEquals(1000, $this->_model->getLastUsedRewriteRequestIncrement('url-key-', '.html', 1));
    }
}
