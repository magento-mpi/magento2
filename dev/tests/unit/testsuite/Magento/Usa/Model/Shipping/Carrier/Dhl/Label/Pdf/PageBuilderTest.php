<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usa\Model\Shipping\Carrier\Dhl\Label\Pdf;

class PageBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testAddDestinationFacilityCodewithUtf8()
    {
        $page = $this->getMockBuilder('\Zend_Pdf_Page')
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects($this->once())
            ->method('drawText')
            ->with(
                $this->equalTo('Nürnberg-Österreich-Zürich'),
                $this->anything(),
                $this->anything(),
                $this->equalTo('UTF-8')
            );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Usa\Model\Shipping\Carrier\Dhl\Label\Pdf\PageBuilder $pageBuilder */
        $pageBuilder = $objectManagerHelper->getObject('\Magento\Usa\Model\Shipping\Carrier\Dhl\Label\Pdf\PageBuilder');
        $pageBuilder->setPage($page);
        $pageBuilder->addDestinationFacilityCode('Nürnberg', 'Österreich', 'Zürich');
    }
}
 