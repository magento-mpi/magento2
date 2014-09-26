<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Model\Plugin\Rss;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class WishlistTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\MultipleWishlist\Model\Plugin\Rss\Wishlist */
    protected $wishlist;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\MultipleWishlist\Helper\Rss|\PHPUnit_Framework_MockObject_MockObject */
    protected $helper;

    /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlInterface;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfig;

    /** @var \Magento\Customer\Helper\View|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerViewHelper;

    /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerAccount;

    protected function setUp()
    {
        $this->helper = $this->getMock('Magento\MultipleWishlist\Helper\Rss', [], [], '', false);
        $this->urlInterface = $this->getMock('Magento\Framework\UrlInterface');

        $this->scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->customerViewHelper = $this->getMock('Magento\Customer\Helper\View', [], [], '', false);
        $this->customerAccount = $this->getMock('Magento\Customer\Service\V1\CustomerAccountServiceInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->wishlist = $this->objectManagerHelper->getObject(
            'Magento\MultipleWishlist\Model\Plugin\Rss\Wishlist',
            [
                'wishlistHelper' => $this->helper,
                'urlBuilder' => $this->urlInterface,
                'scopeConfig' => $this->scopeConfig,
                'customerViewHelper' => $this->customerViewHelper,
                'customerAccountService' => $this->customerAccount
            ]
        );
    }

    /**
     * @dataProvider aroundGetHeaderDataProvider
     *
     * @param bool $multipleEnabled
     * @param int $customerId
     * @param bool $isDefault
     * @param array $expectedResult
     */
    public function testAroundGetHeader($multipleEnabled, $customerId, $isDefault, $expectedResult)
    {

        $subject = $this->getMock('Magento\Wishlist\Model\Rss\Wishlist', [], [], '', false);
        $wishlist = $this->getMock('Magento\Wishlist\Model\Wishlist', [
            'getId',
            'getCustomerId',
            'getName',
            'getSharingCode',
            '__wakeup'
        ], [], '', false);
        $wishlist->expects($this->any())->method('getId')->will($this->returnValue(5));
        $wishlist->expects($this->any())->method('getCustomerId')->will($this->returnValue(8));
        $wishlist->expects($this->any())->method('getName')->will($this->returnValue('Wishlist1'));
        $wishlist->expects($this->any())->method('getSharingCode')->will($this->returnValue('code'));

        $customer = $this->getMock('Magento\Customer\Service\V1\Data\Customer', [], [], '', false);
        $customer->expects($this->any())->method('getId')->will($this->returnValue($customerId));
        $customer->expects($this->any())->method('getEmail')->will($this->returnValue('test@example.com'));

        $this->helper->expects($this->any())->method('getWishlist')->will($this->returnValue($wishlist));
        $this->helper->expects($this->any())->method('getCustomer')->will($this->returnValue($customer));
        $this->helper->expects($this->any())->method('isWishlistDefault')->will($this->returnValue($isDefault));
        $this->helper->expects($this->any())->method('getDefaultWishlistName')->will($this->returnValue('Wishlist1'));

        $this->scopeConfig
            ->expects($this->any())
            ->method('getValue')
            ->with('wishlist/general/multiple_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ->will($this->returnValue($multipleEnabled));

        $this->customerViewHelper->expects($this->any())->method('getCustomerName')->with($customer)
            ->will($this->returnValue('Customer1'));

        $this->customerAccount
            ->expects($this->any())
            ->method('getCustomer')
            ->with(8)
            ->will($this->returnValue($customer));

        $this->urlInterface
            ->expects($this->any())
            ->method('getUrl')
            ->with('wishlist/shared/index', array('code' => 'code'))
            ->will($this->returnValue('http://url.com/rss/feed/index/type/wishlist/wishlist_id/5'));


        $proceed = function () use ($expectedResult) {
            return $expectedResult;
        };

        $this->assertEquals($expectedResult, $this->wishlist->aroundGetHeader($subject, $proceed));
    }

    public function aroundGetHeaderDataProvider()
    {
        return array(
            array(false, 8, true, array(
                'title' => 'title',
                'description' => 'title',
                'link' => 'http://url.com/rss/feed/index/type/wishlist/wishlist_id/5',
                'charset' => 'UTF-8'
            )),
            array(true, 8, true, array(
                'title' => 'Customer1\'s Wish List',
                'description' => 'Customer1\'s Wish List',
                'link' => 'http://url.com/rss/feed/index/type/wishlist/wishlist_id/5',
                'charset' => 'UTF-8'
            )),
            array(true, 8, false, array(
                'title' => 'Customer1\'s Wish List (Wishlist1)',
                'description' => 'Customer1\'s Wish List (Wishlist1)',
                'link' => 'http://url.com/rss/feed/index/type/wishlist/wishlist_id/5',
                'charset' => 'UTF-8'
            )),
            array(true, 9, false, array(
                'title' => 'Customer1\'s Wish List (Wishlist1)',
                'description' => 'Customer1\'s Wish List (Wishlist1)',
                'link' => 'http://url.com/rss/feed/index/type/wishlist/wishlist_id/5',
                'charset' => 'UTF-8'
            )),
        );
    }
}
