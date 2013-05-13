<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Backend_Model_Config_Source_AbstractLocaleTest extends PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        $modelLocale = $this->getMock('Mage_Core_Model_LocaleInterface');
        $modelSourceLocalMock = $this->getMockForAbstractClass('Saas_Backend_Model_Config_Source_AbstractLocale',
            array($modelLocale, array('locale_code_1')), '', true, true, true, array('_getLocales'));

        $modelSourceLocalMock->expects($this->once())->method('_getLocales')
            ->will($this->returnValue(array(
                array('value' => 'locale_code_1', 'label' => 'locale_label_1'),
                array('value' => 'locale_code_2', 'label' => 'locale_label_2')
            )));

        $this->assertEquals(array(
            array('value' => 'locale_code_1', 'label' => 'locale_label_1'),
        ), $modelSourceLocalMock->toOptionArray());
    }

    public function testTestSingleOptionsFiltering()
    {
        $modelSourceLocalMock = $this->getMockForAbstractClass('Saas_Backend_Model_Config_Source_AbstractLocale',
            array(), '', false, true, true, array('_filterLocales'));

        $modelSourceLocalMock->expects($this->once())->method('_filterLocales')
            ->will($this->returnValue(array()));

        $modelSourceLocalMock->toOptionArray();
        $modelSourceLocalMock->toOptionArray();
    }
}
