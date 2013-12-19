<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

class WebsiteTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCanDelete()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $websiteCollection = $this->getMock(
            'Magento\Core\Model\Resource\Website\Collection', array('getSize'), array(), '', false
        );
        $websiteCollection->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(2));

        $websiteFactory = $this->getMock(
            'Magento\Core\Model\WebsiteFactory', array('create', 'getCollection', '__wakeup'), array(), '', false
        );
        $websiteFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($websiteFactory));
        $websiteFactory->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($websiteCollection));

        /** @var \Magento\Core\Model\Website $websiteModel */
        $websiteModel = $objectManager->getObject(
            'Magento\Core\Model\Website', array('websiteFactory' => $websiteFactory)
        );
        $websiteModel->setId(2);
        $this->assertTrue($websiteModel->isCanDelete());
    }
}