<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\DesignEditor\Model\Config\Control;

class QuickStylesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSchemaFile()
    {
        /** @var $moduleReader \Magento\Module\Dir\Reader|PHPUnit_Framework_MockObject_MockObject */
        $moduleReader = $this->getMockBuilder('Magento\Module\Dir\Reader')
            ->setMethods(array('getModuleDir'))
            ->disableOriginalConstructor()
            ->getMock();

        $moduleReader->expects($this->any(), $this->any())
            ->method('getModuleDir')
            ->will($this->returnValue('/base_path/etc'));

        /** @var $quickStyle \Magento\DesignEditor\Model\Config\Control\QuickStyles */
        $quickStyle = $this->getMock('Magento\DesignEditor\Model\Config\Control\QuickStyles', null, array(
            'moduleReader' => $moduleReader, 'configFiles' => array('sample')
        ), '', false);

        $property = new \ReflectionProperty($quickStyle, '_moduleReader');
        $property->setAccessible(true);
        $property->setValue($quickStyle, $moduleReader);

        $this->assertStringMatchesFormat('%s/etc/quick_styles.xsd', $quickStyle->getSchemaFile());
    }
}
