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

namespace Magento\Catalog\Helper\Product;

/**
 * Class CompareTest
 * @package Magento\Catalog\Helper\Product
 */
class CompareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $compareHelper;

    /**
     * @var \Magento\App\Helper\Context | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Url | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Core\Helper\Data | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $postDataHelper;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->urlBuilder = $this->getMock('Magento\Url', array('getUrl'), array(), '', false);
        /** @var \Magento\App\Helper\Context $context */
        $this->context = $this->getMock('Magento\App\Helper\Context', array('getUrlBuilder'), array(), '', false);
        $this->context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->urlBuilder));
        $this->postDataHelper = $this->getMock('Magento\Core\Helper\PostData', array('getPostData'), array(), '', false);

        $this->compareHelper = $objectManager->getObject(
            'Magento\Catalog\Helper\Product\Compare',
            array('context' => $this->context, 'coreHelper' => $this->postDataHelper)
        );
    }

    public function testGetPostDataRemove()
    {
        //Data
        $productId = 1;
        $removeUrl = 'catalog/product_compare/remove';
        $compareListUrl = 'catalog/product_compare';
        $postParams = array(
            \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->compareHelper->urlEncode($compareListUrl),
            'product' => $productId
        );

        //Verification
        $this->urlBuilder->expects($this->at(0))
            ->method('getUrl')
            ->with($compareListUrl)
            ->will($this->returnValue($compareListUrl));
        $this->urlBuilder->expects($this->at(1))
            ->method('getUrl')
            ->with($removeUrl)
            ->will($this->returnValue($removeUrl));
        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($removeUrl, $postParams)
            ->will($this->returnValue(true));

        /** @var \Magento\Catalog\Model\Product | \PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMock('Magento\Catalog\Model\Product', array('getId', '__wakeup'), array(), '', false);
        $product->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($productId));

        $this->assertTrue($this->compareHelper->getPostDataRemove($product));
    }
}
