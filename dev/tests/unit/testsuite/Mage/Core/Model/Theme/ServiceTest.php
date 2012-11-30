<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme service model
 */
class Mage_Core_Model_Theme_ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Core_Model_Theme_Service::isPresentCustomizedThemes
     * @dataProvider isPresentCustomizedThemesDataProvider
     */
    public function testIsPresentCustomizedThemes($availableIsVirtual, $expectedResult)
    {
        $themeCollectionMock = array();
        foreach ($availableIsVirtual as $isVirtual) {
            /** @var $themeItemMock Mage_Core_Model_Theme */
            $themeItemMock = $this->getMock('Mage_Core_Model_Theme', array('isVirtual'), array(), '', false);
            $themeItemMock->expects($this->any())
                ->method('isVirtual')
                ->will($this->returnValue($isVirtual));
            $themeCollectionMock[] = $themeItemMock;
        }

        /** @var $themeMock Mage_Core_Model_Theme */
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('getCollection'), array(), '', false);
        $themeMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($themeCollectionMock));

        $themeService = new Mage_Core_Model_Theme_Service($themeMock);
        $this->assertEquals($expectedResult, $themeService->isPresentCustomizedThemes());
    }

    /**
     * @return array
     */
    public function isPresentCustomizedThemesDataProvider()
    {
        return array(
            array(array(false, false, false), false),
            array(array(false, true, false), true)
        );
    }
}
