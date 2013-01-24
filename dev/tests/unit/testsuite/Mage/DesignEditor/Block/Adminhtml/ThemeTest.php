<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Adminhtml_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @cover Mage_DesignEditor_Block_Adminhtml_Theme::addButton
     * @cover Mage_DesignEditor_Block_Adminhtml_Theme::clearButtons
     * @cover Mage_DesignEditor_Block_Adminhtml_Theme::getButtonsHtml
     */
    public function testButtons()
    {
        $themeMock  =  $this->getMock('Mage_DesignEditor_Block_Adminhtml_Theme', null, array(), '', false);
        $buttonMock = $this->getMock('StdClass', array('toHtml'));

        $buttonMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue('Block html data'));

        $themeMock->addButton($buttonMock);
        $this->assertEquals('Block html data', $themeMock->getButtonsHtml());

        $themeMock->clearButtons();
        $this->assertEquals('', $themeMock->getButtonsHtml());
    }
}
