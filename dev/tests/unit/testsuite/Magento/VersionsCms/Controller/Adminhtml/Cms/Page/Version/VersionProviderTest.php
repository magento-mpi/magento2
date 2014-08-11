<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;

class VersionProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $versionMock = $this->getMockBuilder('Magento\VersionsCms\Model\Page\Version')
            ->setMethods(['loadWithRestrictions', '__wakeup'])->disableOriginalConstructor()->getMock();
        $versionMock->expects($this->once())
            ->method('loadWithRestrictions')
            ->will($this->returnValue($this->returnSelf()));

        $versionFactoryMock = $this->getMockBuilder('Magento\VersionsCms\Model\Page\VersionFactory')
            ->setMethods(['create'])->disableOriginalConstructor()->getMock();
        $versionFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($versionMock));

        $userMock = $this->getMockBuilder('Magento\User\Model\User')
            ->setMethods(['getId', '__wakeup'])->disableOriginalConstructor()->getMock();
        $userMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(5));

        $authSessionMock = $this->getMockBuilder('Magento\Backend\Model\Auth\Session')
            ->setMethods(['getUser'])->disableOriginalConstructor()->getMock();
        $authSessionMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($userMock));

        $cmsConfigMock = $this->getMockBuilder('Magento\VersionsCms\Model\Config')
            ->setMethods(['getAllowedAccessLevel'])->disableOriginalConstructor()->getMock();
        $cmsConfigMock->expects($this->once())
            ->method('getAllowedAccessLevel')
            ->will($this->returnValue([1, 2, 3]));

        $registryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->setMethods(['register'])->disableOriginalConstructor()->getMock();
        $registryMock->expects($this->once())
            ->method('register')
            ->will($this->returnSelf());

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version\VersionProvider $versionProvider */
        $versionProvider = $objectManager->getObject(
            'Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version\VersionProvider',
            [
                'registry' => $registryMock,
                'pageVersionFactory' => $versionFactoryMock,
                'authSession' => $authSessionMock,
                'cmsConfig' => $cmsConfigMock
            ]
        );

        $this->assertInstanceOf('\Magento\VersionsCms\Model\Page\Version', $versionProvider->get(20));
    }
}
