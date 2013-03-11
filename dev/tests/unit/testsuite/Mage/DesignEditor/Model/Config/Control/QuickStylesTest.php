<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Config_Control_QuickStylesTest extends PHPUnit_Framework_TestCase
{
    public function testGetSchemaFile()
    {
        /** @var $quickStyle Mage_DesignEditor_Model_Config_Control_QuickStyles */
        $quickStyle = $this->getMock('Mage_DesignEditor_Model_Config_Control_QuickStyles', null, array(), '', false);
        $this->assertContains('/../../../etc/quick_styles.xsd', $quickStyle->getSchemaFile());
    }
}
