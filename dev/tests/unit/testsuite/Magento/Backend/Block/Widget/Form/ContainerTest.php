<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Block\Widget\Form;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDataObject()
    {
        $form = new \Magento\Object;
        $dataObject = new \Magento\Object;

        // _prepateLayout() is blocked, because it is used by block to instantly add 'form' child
        $block = $this->getMock('Magento\Backend\Block\Widget\Form\Container', array('getChildBlock'), array(), '',
            false);
        $block->expects($this->once())
            ->method('getChildBlock')
            ->with('form')
            ->will($this->returnValue($form));

        $block->setDataObject($dataObject);
        $this->assertSame($dataObject, $block->getDataObject());
        $this->assertSame($dataObject, $form->getDataObject());
    }
}
