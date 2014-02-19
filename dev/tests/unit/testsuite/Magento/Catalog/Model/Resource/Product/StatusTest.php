<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Resource\Product;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Status
     */
    protected $_model;


    /**
     * @var \Magento\Core\Model\Store
     */
    protected $_store;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManagerInterface;

    /**
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $_product;

    public function setUp()
    {
        $this->_product = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product',
            array(), array(), '', false
        );

        $this->_store = $this->getMock(
            '\Magento\Core\Model\Store',
            array(), array(), '', false
        );

        $this->_storeManagerInterface = $this->getMock(
            '\Magento\Core\Model\StoreManagerInterface');

        $this->_storeManagerInterface->expects($this->any())
            ->method('getDefaultStoreView')
            ->will($this->returnValue($this->_store));


        $this->_model = new \Magento\Catalog\Model\Resource\Product\Status(
            $this->getMock('Magento\App\Resource', array(), array(), '', false),
            $this->_product,
            $this->_storeManagerInterface
        );
    }

    public function testGetProductAttribute()
    {
        $this->_product->expects($this->once())
            ->method('getAttribute');

        $this->_model->getProductAttribute('test');
    }
}
