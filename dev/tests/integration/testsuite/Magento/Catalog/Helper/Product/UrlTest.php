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

namespace Magento\Catalog\Helper\Product;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Product\Url
     */
    protected $_helper;

    public static function setUpBeforeClass()
    {
        /** @var $configModel \Magento\Core\Model\Config */
        $configModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Config');
        // @todo re-implement this test
        $data = array(
            'from' => '™',
            'to' => 'TM',
        );
        $configModel->setValue('url/convert/char8482', $data);
    }

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
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
