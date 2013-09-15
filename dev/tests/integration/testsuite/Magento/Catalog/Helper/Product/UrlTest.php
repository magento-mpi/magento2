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

class Magento_Catalog_Helper_Product_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Product\Url
     */
    protected $_helper;

    public static function setUpBeforeClass()
    {
        // @todo re-implement this test
        $data = array(
            'from' => '™',
            'to' => 'TM',
        );
        Mage::getConfig()->setValue('url/convert/char8482', $data);
    }

    protected function setUp()
    {
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Helper\Product\Url');
    }

    public function testGetConvertTable()
    {
        $convertTable = $this->_helper->getConvertTable();
        $this->assertInternalType('array', $convertTable);
        $this->assertNotEmpty($convertTable);
    }

    public function testFormat()
    {
        $this->assertEquals('TM', $this->_helper->format('™'));
    }
}
