<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Flat;

class IndexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Flat\Indexer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Resource\Product\Flat\Indexer');
    }

    public function testGetAttributeCodes()
    {
        $actualResult = $this->_model->getAttributeCodes();
        $this->assertContains('name', $actualResult);
        $this->assertContains('price', $actualResult);
        $nameAttributeId = array_search('name', $actualResult);
        $priceAttributeId = array_search('price', $actualResult);
        $this->assertGreaterThan(0, $nameAttributeId, 'Id of the attribute "name" must be valid');
        $this->assertGreaterThan(0, $priceAttributeId, 'Id of the attribute "name" must be valid');
        $this->assertNotEquals($nameAttributeId, $priceAttributeId, 'Attribute ids must be different');
    }
}
