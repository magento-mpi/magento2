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

class AdminTokenServiceTest extends \PHPUnit_Framework_TestCase
{
    /** \Magento\Integration\Service\V1\TokenService */
    protected $_tokenService;

    /** \Magento\Integration\Model\Oauth\Token\Factory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenModelFactoryMock;

    /** \Magento\User\Model\User|\PHPUnit_Framework_MockObject_MockObject */
    protected $_userModelMock;

    /** \Magento\Integration\Model\Resource\Oauth\Token\Collection|\PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenModelCollectionMock;

    /** \Magento\Integration\Model\Resource\Oauth\Token\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenModelCollectionFactoryMock;

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

        $this->_customerAccountServiceMock = $this
            ->getMockBuilder('Magento\Customer\Service\V1\CustomerAccountServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_tokenMock = $this->getMockBuilder('Magento\Integration\Model\Oauth\Token')
            ->disableOriginalConstructor()
            ->setMethods(['getToken', 'loadByAdminId', 'setRevoked', 'save', '__wakeup'])->getMock();

        $this->_tokenModelCollectionMock = $this->getMockBuilder('Magento\Integration\Model\Resource\Oauth\Token\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['addFilterByAdminId', 'getSize', '__wakeup', '_beforeLoad', '_afterLoad', 'getIterator'])->getMock();

        $this->_tokenModelCollectionFactoryMock = $this->getMockBuilder('Magento\Integration\Model\Resource\Oauth\Token\CollectionFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->_tokenModelCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_tokenModelCollectionMock));

        $this->_tokenService = new \Magento\Integration\Service\V1\TokenService(
            $this->_tokenModelFactoryMock,
            $this->_userModelMock,
            $this->_customerAccountServiceMock,
            $this->_tokenModelCollectionFactoryMock
        );

    }

    public function testRevokeAdminAccessToken()
    {
        $adminId = 1;

        $this->_tokenModelCollectionMock->expects($this->once())
            ->method('addFilterByAdminId')
            ->with($adminId)
            ->will($this->returnValue($this->_tokenModelCollectionMock));
        $this->_tokenModelCollectionMock->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(1));
        $this->_tokenModelCollectionMock->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$this->_tokenMock])));
        $this->_tokenModelCollectionMock->expects($this->any())
            ->method('_fetchAll')
            ->with(null)
            ->will($this->returnValue(1));
        $this->_tokenMock->expects($this->once())
            ->method('setRevoked')
            ->will($this->returnValue($this->_tokenMock));
        $this->_tokenMock->expects($this->once())
            ->method('save');

        $this->assertTrue($this->_tokenService->revokeAdminAccessToken($adminId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage This user has no tokens.
     */
    public function testRevokeAdminAccessTokenWithoutAdminId()
    {
        $this->_tokenModelCollectionMock->expects($this->once())
            ->method('addFilterByAdminId')
            ->with(null)
            ->will($this->returnValue($this->_tokenModelCollectionMock));
        $this->_tokenMock->expects($this->never())
            ->method('save');
        $this->_tokenMock->expects($this->never())
            ->method('setRevoked')
            ->will($this->returnValue($this->_tokenMock));
        $this->_tokenService->revokeAdminAccessToken(null);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The tokens could not be revoked.
     */
    public function testRevokeAdminAccessTokenCannotRevoked()
    {
        $exception = new \Exception();
        $adminId = 1;
        $this->_tokenModelCollectionMock->expects($this->once())
            ->method('addFilterByAdminId')
            ->with($adminId)
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
        $this->_tokenService->revokeAdminAccessToken($adminId);
    }
}


