<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rule\Model\Renderer;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ActionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rule\Model\Renderer\Actions
     */
    protected $actions;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\Data\Form\Element\AbstractElement|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_element;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->actions = $this->objectManagerHelper->getObject('Magento\Rule\Model\Renderer\Actions');
        $this->_element = $this->getMock(
            '\Magento\Framework\Data\Form\Element\AbstractElement',
            ['getRule'],
            [],
            '',
            false
        );
    }

    public function testRender()
    {
        $rule = $this->getMock('\Magento\Rule\Model\Rule', ['getActions', '__sleep', '__wakeup'], [], '', false);
        $actions = $this->getMock('\Magento\Rule\Model\Action\Collection', ['asHtmlRecursive'], [], '', false);

        $this->_element->expects($this->any())
            ->method('getRule')
            ->will($this->returnValue($rule));

        $rule->expects($this->any())
            ->method('getActions')
            ->will($this->returnValue($actions));

        $actions->expects($this->once())
            ->method('asHtmlRecursive')
            ->will($this->returnValue('action html'));

        $this->assertEquals('action html', $this->actions->render($this->_element));
    }
}
