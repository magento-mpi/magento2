<?php
/**
 * Test for \Magento\Integration\Service\V1\TokenService
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Integration\Model\Integration;
use Magento\Integration\Model\Oauth\Token;

class TokenServiceTest extends \PHPUnit_Framework_TestCase
{
    /** \Magento\Integration\Service\V1\TokenService */
    protected $_tokenService;

    /** \Magento\Integration\Model\Oauth\Token\Factory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenModelFactoryMock;

    /** \Magento\User\Model\User|\PHPUnit_Framework_MockObject_MockObject */
    protected $_userModelMock;

    /** \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_customerAccountServiceMock;

    /**
     * @var \Magento\Integration\Model\Oauth\Token|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_tokenMock;

    protected function setUp()
    {
        $this->_tokenModelFactoryMock = $this->getMockBuilder('Magento\Integration\Model\Oauth\Token\Factory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();


        $this->_userModelMock = $this->getMockBuilder('Magento\User\Model\User')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_customerAccountServiceMock = $this->getMockBuilder('Magento\Customer\Service\V1\CustomerAccountServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_tokenMock = $this->getMockBuilder('Magento\Integration\Model\Oauth\Token')
            ->disableOriginalConstructor()->setMethods(['getToken', 'loadByCustomerId', 'setRevoked', 'save', '__wakeup'])->getMock();

        $this->_tokenModelFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->_tokenMock));

        $this->_tokenService = new \Magento\Integration\Service\V1\TokenService(
            $this->_tokenModelFactoryMock,
            $this->_userModelMock,
            $this->_customerAccountServiceMock
        );
    }

    public function testRevokeCustomerAccessToken()
    {
        $customerId = 1;

        $this->_tokenMock->expects($this->once())->method('getToken')->will($this->returnValue('test'));
        $this->_tokenMock->expects($this->once())->method('loadByCustomerId')->will($this->returnValue($this->_tokenMock));
        $this->_tokenMock->expects($this->once())->method('save');
        $this->_tokenMock->expects($this->once())->method('setRevoked')->will($this->returnValue($this->_tokenMock));
        $this->_tokenMock->expects($this->once())->method('loadByCustomerId')->with($customerId)->will($this->returnValue($this->_tokenService));
        $this->assertTrue($this->_tokenService->revokeCustomerAccessToken($customerId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Token  does not exist.
     */
    public function testRevokeCustomerAccessTokenWithoutCustomerId()
    {
        $this->_tokenMock->expects($this->exactly(2))->method('getToken')->will($this->returnValue(''));
        $this->_tokenMock->expects($this->once())->method('loadByCustomerId')->will($this->returnValue($this->_tokenMock));
        $this->_tokenMock->expects($this->never())->method('save');
        $this->_tokenMock->expects($this->never())->method('setRevoked')->will($this->returnValue($this->_tokenMock));
        $this->_tokenService->revokeCustomerAccessToken(null);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Token test could not be revoked.
     */
    public function testRevokeCustomerAccessTokenCannotRevoked()
    {
        $exception = new \Exception();
        $customerId = 1;
        $this->_tokenMock->expects($this->any())->method('getToken')->will($this->returnValue('test'));
        $this->_tokenMock->expects($this->once())->method('loadByCustomerId')->will($this->returnValue($this->_tokenMock));
        $this->_tokenMock->expects($this->never())->method('save');
        $this->_tokenMock->expects($this->once())->method('setRevoked')->will($this->throwException($exception));
        $this->_tokenService->revokeCustomerAccessToken($customerId);
    }
}


