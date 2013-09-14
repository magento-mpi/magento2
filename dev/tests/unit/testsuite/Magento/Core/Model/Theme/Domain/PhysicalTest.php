<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme domain physical model
 */
class Magento_Core_Model_Theme_Domain_PhysicalTest extends PHPUnit_Framework_TestCase
{
    public function testCreateVirtualTheme()
    {
        $physicalTheme = $this->getMock('Magento\Core\Model\Theme', null, array(), '', false, false);
        $physicalTheme->setData(array(
            'parent_id' => 10,
            'theme_title' => 'Test Theme'
        ));

        $copyService = $this->getMock('Magento\Core\Model\Theme\CopyService', array('copy'), array(), '', false, false);
        $copyService->expects($this->once())
            ->method('copy')
            ->will($this->returnValue($copyService));

        $virtualTheme = $this->getMock(
            'Magento\Core\Model\Theme', array('getThemeImage', 'createPreviewImageCopy', 'save'),
            array(), '', false, false
        );
        $virtualTheme->expects($this->once())
            ->method('getThemeImage')
            ->will($this->returnValue($virtualTheme));

        $virtualTheme->expects($this->once())
            ->method('createPreviewImageCopy')
            ->will($this->returnValue($virtualTheme));

        $virtualTheme->expects($this->once())
            ->method('save')
            ->will($this->returnValue($virtualTheme));

        $themeFactory = $this->getMock('Magento\Core\Model\ThemeFactory', array('create'), array(), '', false, false);
        $themeFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($virtualTheme));

        $themeCollection = $this->getMock(
            'Magento\Core\Model\Resource\Theme\Collection',
            array('addTypeFilter', 'addAreaFilter', 'addFilter', 'count'),
            array(), '', false, false
        );

        $themeCollection->expects($this->any())
            ->method('addTypeFilter')
            ->will($this->returnValue($themeCollection));

        $themeCollection->expects($this->any())
            ->method('addAreaFilter')
            ->will($this->returnValue($themeCollection));

        $themeCollection->expects($this->any())
            ->method('addFilter')
            ->will($this->returnValue($themeCollection));

        $themeCollection->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1));

        $domainModel = new \Magento\Core\Model\Theme\Domain\Physical(
            $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false, false),
            $themeFactory,
            $copyService,
            $themeCollection
        );
        $domainModel->createVirtualTheme($physicalTheme);
    }
}
