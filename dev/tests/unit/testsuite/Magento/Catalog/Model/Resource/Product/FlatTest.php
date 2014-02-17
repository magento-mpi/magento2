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

class FlatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Flat
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

    public function setUp()
    {
        $this->_store = $this->getMock(
            '\Magento\Core\Model\Store',
            array(), array(), '', false
        );

        $this->_storeManagerInterface = $this->getMock(
            '\Magento\Core\Model\StoreManagerInterface');

        $this->_storeManagerInterface->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_store));

        $this->_storeManagerInterface->expects($this->any())
            ->method('getDefaultStoreView')
            ->will($this->returnValue($this->_store));


        $this->_model = new \Magento\Catalog\Model\Resource\Product\Flat(
            $this->getMock('Magento\App\Resource', array(), array(), '', false),
            $this->_storeManagerInterface,
            $this->getMock('Magento\Catalog\Model\Config', array(), array(), '', false)
        );
    }

    public function testSetIntStoreId()
    {
        $store = $this->_model->setStoreId(1);
        $storeId = $store->getStoreId();
        $this->assertEquals(1, $storeId);
    }

    public function testSetNotIntStoreId()
    {
        $this->_storeManagerInterface->expects($this->once())
            ->method('getStore');

        $store = $this->_model->setStoreId('test');
        $storeId = $store->getStoreId();
        $this->assertEquals(0, $storeId);
    }
}
