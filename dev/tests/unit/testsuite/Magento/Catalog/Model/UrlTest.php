<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Url
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_categoryModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_categoryFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_categoryHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_rewriteModel;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $this->_resourceModel = $this->getMock(
            '\Magento\Catalog\Model\Resource\Url',
            array(
                '__wakeup',
                'getStores',
                'getProductsByStore',
                'getCategories',
                'getCategory',
            ),
            array(),
            '',
            false
        );
        $this->_urlFactory = $this->getMock(
            '\Magento\Catalog\Model\Resource\UrlFactory',
            array(
                'create',
                'formatUrlKey',
                'getUrlPath'
            )
        );
        $this->_storeModel = $this->getMock(
            '\Magento\Store\Model\Store',
            array(
                '__wakeup',
                'getId',
                'getRootCategoryId'
            ),
            array(),
            '',
            false
        );
        $this->_productModel = $this->getMock(
            'Magento\Catalog\Model\Product',
            array(
                '__wakeup',
                'getCategoryIds',
                'getId',
                'getResource',
                'getUrlPath'
            ),
            array(),
            '',
            false
        );
        $this->_categoryModel = $this->getMock(
            'Magento\Catalog\Model\Category',
            array(
                '__wakeup',
                'getId',
                'getStoreId',
                'getChilds',
                'formatUrlKey',
                'getUrlKey',
                'getName'
            ),
            array(),
            '',
            false
        );
        $this->_categoryFactory = $this->getMock('\Magento\Catalog\Model\CategoryFactory');
        $this->_categoryHelper = $this->getMock(
            'Magento\Catalog\Helper\Category',
            array(),
            array(),
            '',
            false
        );
        // @TODO: UrlRewrite
        $this->_rewriteModel = $this->getMock(
            'Magento\UrlRewrite\Model\UrlRewrite',
            array(
                '__wakeup',
                'getRequestPath'
            ),
            array(),
            '',
            false
        );

        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $this->_objectManager->getObject(
            'Magento\Catalog\Model\Url',
            array(
                'urlFactory' => $this->_urlFactory,
                'catalogCategoryFactory' => $this->_categoryFactory,
                'catalogCategory' => $this->_categoryHelper
            )
        );

        $this->_urlFactory->expects($this->any())->method('create')
            ->will($this->returnValue($this->_resourceModel));
        $this->_resourceModel->expects($this->any())->method('getCategory')
            ->will($this->returnValue($this->_categoryModel));
        $this->_categoryModel->expects($this->any())->method('getId')
            ->will($this->returnValue(1));
        $this->_categoryModel->expects($this->any())->method('getStoreId')
            ->will($this->returnValue(1));
        $this->_categoryModel->expects($this->any())->method('getChilds')
            ->will($this->returnSelf());
        $this->_storeModel->expects($this->any())->method('getId')
            ->will($this->returnValue(1));
    }
}
