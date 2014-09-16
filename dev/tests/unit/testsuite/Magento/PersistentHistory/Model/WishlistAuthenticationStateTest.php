<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model;

class WishlistAuthenticationStateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $phHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentSessionMock;

    /**
     * @var \Magento\PersistentHistory\Model\WishlistAuthenticationState
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->phHelperMock = $this->getMock(
            '\Magento\PersistentHistory\Helper\Data',
            ['isWishlistPersist'],
            [],
            '',
            false
        );
        $this->persistentSessionMock = $this->getMock(
            '\Magento\Persistent\Helper\Session',
            ['isPersistent'],
            [],
            '',
            false
        );
        $this->subject = $objectManager->getObject(
            '\Magento\PersistentHistory\Model\WishlistAuthenticationState',
            ['phHelper' => $this->phHelperMock, 'persistentSession' => $this->persistentSessionMock]
        );
    }

    public function testIsAuthEnabledIfPersistentSessionNotPersistent()
    {
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(false));
        $this->assertTrue($this->subject->isEnabled());
    }

    public function testIsAuthEnabledIfwishlistNotPersistent()
    {
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->phHelperMock->expects($this->once())->method('isWishlistPersist')->will($this->returnValue(false));
        $this->assertTrue($this->subject->isEnabled());
    }

    public function testIsAuthEnabled()
    {
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->phHelperMock->expects($this->once())->method('isWishlistPersist')->will($this->returnValue(true));
        $this->assertFalse($this->subject->isEnabled());
    }
}
 