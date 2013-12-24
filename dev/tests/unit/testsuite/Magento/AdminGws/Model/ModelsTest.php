<?php
/**
* {license_notice}
*
* @category    Magento
* @package     Magento_AdminGws
* @subpackage  unit_tests
* @copyright   {copyright}
* @license     {license_link}
*/

namespace Magento\AdminGws\Model;

/**
 * Test class for Enterprise\AdminGws\Model\Models
 */
class ModelsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectHelper;

    /**
     * Role Model Instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_role;

    /**
     * Model Instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * Factory Instance
     *
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factory;

    /**
     * Model Class Name
     *
     * @var string
     */
    protected $_className = '\Magento\AdminGws\Model\Models';

    /**
     * Category Factory Class Name
     *
     * @var string
     */
    protected $_categoryFactoryClassName = '\Magento\Catalog\Model\CategoryFactory';

    /**
     * Role Class Name
     *
     * @var string
     */
    protected $_roleClassName = '\Magento\AdminGws\Model\Role';

    /**
     * Helper Data Class Name
     *
     * @var string
     */
    protected $_dataClassName = '\Magento\AdminGws\Helper\Data';

    /**
     * Store Manager Interface Name
     *
     * @var string
     */
    protected $_storeManagerClassName = '\Magento\Core\Model\StoreManagerInterface';

    /**
     * Factory Class Name
     *
     * @var string
     */
    protected $_productFactoryClass = 'Magento\Catalog\Model\ProductFactory';

    /**
     * Sales Model Order Class
     *
     * @var string
     */
    protected $_salesModelClass = '\Magento\Sales\Model\Order';

    /**
     * Sales Model Order Class
     *
     * @var string
     */
    protected $_storeClass = '\Magento\Core\Model\Store';

    /**
     * Sales mock object
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_salesMock;

    /**
     * Store Mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * Product Factory mock object
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productFactoryMock;

    /**
     * Category Factory mock object
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_categoryFactoryMock;

    /**
     * Helper Data mock object
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataMock;

    /**
     * Store Interface mock object
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * Product Class
     *
     * @var string
     */
    protected $_catalogProductClass = '\Magento\Catalog\Model\Product';

    /**
     * Catalog Product Model Mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_catalogProductMock;

    /**
     * Initialize model instance
     */
    protected function setUp()
    {
        $this->_objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_productFactoryMock = $this->getMock($this->_productFactoryClass,
            array('create', '__wakeup'), array(), '', false);
        $this->_categoryFactoryMock = $this->getMock($this->_categoryFactoryClassName,
            array('__wakeup'), array(), '', false);
        $this->_role = $this->getMock($this->_roleClassName,
            array('getWebsiteIds', '__wakeup'), array(), '', false);
        $this->_dataMock = $this->getMock($this->_dataClassName, array('__wakeup'), array(), '', false);
        $this->_storeManagerMock = $this->getMockBuilder($this->_storeManagerClassName)
            ->setMethods(array('__wakeup'))
            ->getMockForAbstractClass();
        $this->_storeMock = $this->getMock($this->_storeClass, array('__wakeup', 'getWebsiteId'), array(), '', false);

        $this->_object = $this->_objectHelper->getObject($this->_className, array(
            'role' => $this->_role,
            'adminGwsData' => $this->_dataMock,
            'categoryFactory' => $this->_categoryFactoryMock,
            'productFactory' => $this->_productFactoryMock,
            'storeManager' => $this->_storeManagerMock
        ));

        $this->_salesMock = $this->getMock($this->_salesModelClass,
            array('__wakeup', 'getAllItems', 'getStore', 'setActionFlag'), array(), '', false);
    }

    /**
     * Test Order validate after load
     *
     * @param array $websiteIds
     * @param \Magento\Object
     * @dataProvider setOrderActionFlagDataProvider
     */
    public function testSalesOrderLoadAfterNoException($websiteIds, $products)
    {
        $allProductsAvailable = true;

        $this->_salesMock->expects($this->once())
            ->method('getAllItems')
            ->will($this->returnValue($products));

        $this->_catalogProductMock = $this->getMockBuilder($this->_catalogProductClass)
            ->disableOriginalConstructor()
            ->setMethods(array('__wakeup', 'load', 'getProductId'))
            ->getMock();

        $index = 0;
        foreach ($products as $product) {
                $this->_productFactoryMock->expects($this->at($index))
                    ->method('create')
                    ->will($this->returnValue($this->_catalogProductMock));

            if (!$product->getProductId()) {
                $this->_catalogProductMock->expects($this->at($index))
                    ->method('load')
                    ->with($product->getProductId())
                    ->will($this->throwException(new \Magento\AdminGws\Controller\Exception()));
            } else {
                $this->_catalogProductMock->expects($this->at($index))
                    ->method('load')
                    ->with($product->getProductId())
                    ->will($this->returnValue($this->_catalogProductMock));
            }

            if (!$product->getProductId()) {
                break;
            }
            $index++;
        }

        $this->_setOrderActionFlag($websiteIds, $allProductsAvailable);

        $this->_object->salesOrderLoadAfter($this->_salesMock);
    }

    /**
     * SetActionFlag method
     *
     * @param array $websiteIds
     * @param bool $allProductsAvailable
     */
    protected function _setOrderActionFlag($websiteIds, $allProductsAvailable)
    {
        $websiteId = 1;
        $flagsArray = array(
            \Magento\Sales\Model\Order::ACTION_FLAG_CANCEL,
            \Magento\Sales\Model\Order::ACTION_FLAG_CREDITMEMO,
            \Magento\Sales\Model\Order::ACTION_FLAG_EDIT,
            \Magento\Sales\Model\Order::ACTION_FLAG_HOLD,
            \Magento\Sales\Model\Order::ACTION_FLAG_INVOICE,
            \Magento\Sales\Model\Order::ACTION_FLAG_REORDER,
            \Magento\Sales\Model\Order::ACTION_FLAG_SHIP,
            \Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD,
            \Magento\Sales\Model\Order::ACTION_FLAG_COMMENT
        );
        $deniedFlag = \Magento\Sales\Model\Order::ACTION_FLAG_PRODUCTS_PERMISSION_DENIED;

        $this->_storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->will($this->returnValue($websiteId));
        $this->_salesMock->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($this->_storeMock));
        $this->_role->expects($this->once())
            ->method('getWebsiteIds')
            ->will($this->returnValue($websiteIds));

        if (!in_array($websiteId, $websiteIds) || !$allProductsAvailable) {
        $i = 2;
            foreach ($flagsArray as $flag) {
                $this->_salesMock->expects($this->at($i++))
                    ->method('setActionFlag')
                    ->with($this->equalTo($flag), $this->equalTo(false))
                    ->will($this->returnSelf());
            }
        }

        if (!$allProductsAvailable) {
            $this->_salesMock->expects($this->at($i))
                ->method('setActionFlag')
                ->with($this->equalTo($deniedFlag), $this->equalTo(true));
        }
    }

    /**
     * @return array
     */
    public function setOrderActionFlagDataProvider()
    {
        return array(
            array(array(1, 2, 3),
                array(
                    new \Magento\Object(array('product_id' => 1)),
                    new \Magento\Object(array('product_id' => 3)),
                    new \Magento\Object(array('product_id' => 4))
                )),
            array(array(4, 2, 3),
                array(
                    new \Magento\Object(array('product_id' => 1)),
                    new \Magento\Object(array('product_id' => 3)),
                    new \Magento\Object(array('product_id' => 4))
                )),
            array(array(4, 2, 3),
                array(
                    new \Magento\Object(array('product_id' => 3)),
                    new \Magento\Object(array('product_id' => NULL)),
                    new \Magento\Object(array('product_id' => 4))
                ))
        );
    }

}