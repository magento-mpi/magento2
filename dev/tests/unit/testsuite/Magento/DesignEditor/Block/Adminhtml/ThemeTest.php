<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_DesignEditor_Block_Adminhtml_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @cover \Magento\DesignEditor\Block\Adminhtml\Theme::addButton
     * @cover \Magento\DesignEditor\Block\Adminhtml\Theme::clearButtons
     * @cover \Magento\DesignEditor\Block\Adminhtml\Theme::getButtonsHtml
     */
    public function testButtons()
    {
        $themeMock  =  $this->getMock('Magento\DesignEditor\Block\Adminhtml\Theme', null, array(), '', false);
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
