<?php
/**
 * Test for \Magento\Integration\Service\V1\CustomerTokenService
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Integration\Model\Integration;
use Magento\Integration\Model\Oauth\Token;

class CustomerTokenServiceTest extends \PHPUnit_Framework_TestCase
{
    /** \Magento\Integration\Service\V1\CustomerTokenService */
    protected $_tokenService;

    /** \Magento\Integration\Model\Oauth\Token\Factory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenModelFactoryMock;

    /** \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_customerAccountServiceMock;

    /** \Magento\Integration\Model\Resource\Oauth\Token\Collection|\PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenModelCollectionMock;

    /** \Magento\Integration\Model\Resource\Oauth\Token\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenModelCollectionFactoryMock;

    /** @var \Magento\Integration\Helper\Validator|\PHPUnit_Framework_MockObject_MockObject */
    protected $validatorHelperMock;

    /** @var \Magento\Integration\Model\Oauth\Token|\PHPUnit_Framework_MockObject_MockObject */
    private $_tokenMock;

    protected function setUp()
    {
        $this->_tokenModelFactoryMock = $this->getMockBuilder('Magento\Integration\Model\Oauth\Token\Factory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->_customerAccountServiceMock = $this
            ->getMockBuilder('Magento\Customer\Service\V1\CustomerAccountServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_tokenMock = $this->getMockBuilder('Magento\Integration\Model\Oauth\Token')
            ->disableOriginalConstructor()
            ->setMethods(['getToken', 'loadByCustomerId', 'setRevoked', 'save', '__wakeup'])->getMock();

        $this->_tokenModelCollectionMock = $this->getMockBuilder(
            'Magento\Integration\Model\Resource\Oauth\Token\Collection'
        )->disableOriginalConstructor()->setMethods(
            ['addFilterByCustomerId', 'getSize', '__wakeup', '_beforeLoad', '_afterLoad', 'getIterator']
        )->getMock();

        $this->_tokenModelCollectionFactoryMock = $this->getMockBuilder(
            'Magento\Integration\Model\Resource\Oauth\Token\CollectionFactory'
        )->setMethods(['create'])->disableOriginalConstructor()->getMock();

        $this->_tokenModelCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_tokenModelCollectionMock));

        $this->validatorHelperMock = $this->getMockBuilder(
            'Magento\Integration\Helper\Validator'
        )->disableOriginalConstructor()->getMock();

        $this->_tokenService = new \Magento\Integration\Service\V1\CustomerTokenService(
            $this->_tokenModelFactoryMock,
            $this->_customerAccountServiceMock,
            $this->_tokenModelCollectionFactoryMock,
            $this->validatorHelperMock
        );

    }

    public function testRevokeCustomerAccessToken()
    {
        $customerId = 1;

        $this->_tokenModelCollectionMock->expects($this->once())
            ->method('addFilterByCustomerId')
            ->with($customerId)
            ->will($this->returnValue($this->_tokenModelCollectionMock));
        $this->_tokenModelCollectionMock->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(1));
        $this->_tokenModelCollectionMock->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$this->_tokenMock])));
        $this->_tokenModelCollectionMock->expects($this->any())
            ->method('_fetchAll')
            ->will($this->returnValue(1));
        $this->_tokenMock->expects($this->once())
            ->method('setRevoked')
            ->will($this->returnValue($this->_tokenMock));
        $this->_tokenMock->expects($this->once())
            ->method('save');

        $this->assertTrue($this->_tokenService->revokeCustomerAccessToken($customerId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage This customer has no tokens.
     */
    public function testRevokeCustomerAccessTokenWithoutCustomerId()
    {
        $this->_tokenModelCollectionMock->expects($this->once())
            ->method('addFilterByCustomerId')
            ->with(null)
            ->will($this->returnValue($this->_tokenModelCollectionMock));
        $this->_tokenMock->expects($this->never())
            ->method('save');
        $this->_tokenMock->expects($this->never())
            ->method('setRevoked')
            ->will($this->returnValue($this->_tokenMock));
        $this->_tokenService->revokeCustomerAccessToken(null);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The tokens could not be revoked.
     */
    public function testRevokeCustomerAccessTokenCannotRevoked()
    {
        $exception = new \Exception();
        $customerId = 1;
        $this->_tokenModelCollectionMock->expects($this->once())
            ->method('addFilterByCustomerId')
            ->with($customerId)
            ->will($this->returnValue($this->_tokenModelCollectionMock));
        $this->_tokenModelCollectionMock->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(1));
        $this->_tokenModelCollectionMock->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$this->_tokenMock])));

        $this->_tokenMock->expects($this->never())
            ->method('save');
        $this->_tokenMock->expects($this->once())
            ->method('setRevoked')
            ->will($this->throwException($exception));
        $this->_tokenService->revokeCustomerAccessToken($customerId);
    }
}


