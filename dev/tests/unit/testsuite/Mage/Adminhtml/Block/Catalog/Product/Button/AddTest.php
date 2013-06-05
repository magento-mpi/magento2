<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Button_AddTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Catalog_Model_Product_Limitation|PHPUnit_Framework_MockObject_MockObject */
    protected $_limitation;

    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Button_Add
     */
    protected $_model;

    public function setUp()
    {
        $context = $this->getMock('Mage_Core_Block_Template_Context', array(), array(), '', false);
        $this->_limitation = $this->getMock('Mage_Catalog_Model_Product_Limitation', array(), array(), '', false);
        $this->_model = new Mage_Adminhtml_Block_Catalog_Product_Button_Add($context, $this->_limitation);

    }

    /**
     * @param bool $isCreateRestricted
     * @param bool $expected
     * @dataProvider hasSplitDataProvider
     */
    public function testHasSplit($isCreateRestricted, $expected)
    {
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue($isCreateRestricted));
        $actual = $this->_model->hasSplit();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public static function hasSplitDataProvider()
    {
        return array(
            'split exists, if the limit is not reached' => array(
                false,
                true
            ),
            'split removed, if the limit is reached' => array(
                true,
                false
            ),
        );
    }

    /**
     * @param bool|null $forcedDisabled
     * @param bool $isCreateRestricted
     * @param bool $expected
     * @dataProvider getDisabledDataProvider
     */
    public function testGetDisabled($forcedDisabled, $isCreateRestricted, $expected)
    {
        $this->_limitation->expects($this->any())
            ->method('isCreateRestricted')
            ->will($this->returnValue($isCreateRestricted));
        if ($forcedDisabled !== null) {
            $this->_model->setDisabled($forcedDisabled);
        }

        $actual = $this->_model->getDisabled();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public static function getDisabledDataProvider()
    {
        return array(
            'forced disabled' => array(
                true,
                false,
                true
            ),
            'forced enabled' => array(
                false,
                true,
                false
            ),
            'disabled, if the limit is reached' => array(
                null,
                true,
                true
            ),
            'enabled, if the limit is not reached' => array(
                null,
                false,
                false
            ),
        );
    }
}
