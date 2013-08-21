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

class Magento_Catalog_Model_Resource_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Resource_Url
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getResourceModel('Magento_Catalog_Model_Resource_Url');
    }

    /**
     * @magentoDataFixture Magento/Catalog/Model/Resource/_files/url_rewrites.php
     */
    public function testGetLastUsedRewriteRequestIncrement()
    {
        $this->assertEquals(1000, $this->_model->getLastUsedRewriteRequestIncrement('url-key-', '.html', 1));
    }
}
