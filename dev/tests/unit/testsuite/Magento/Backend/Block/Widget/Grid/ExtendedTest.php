<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Test class for \Magento\Backend\Model\Url
 */
namespace Magento\Backend\Block\Widget\Grid;

class ExtendedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testPrepareLoadedCollection()
    {
        $request = $this->getMock('Magento\Framework\App\Request\Http', ['has'], [], '', false);
        $request->expects($this->any())->method('has')->will($this->returnValue(null));

        $columnSet = $this->getMock('\Magento\Backend\Block\Widget\Grid\ColumnSet', [], [], '', false);
        $layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $layout->expects($this->any())->method('getChildName')->will($this->returnValue('grid.columnSet'));
        $layout->expects($this->any())->method('getBlock')->will($this->returnValue($columnSet));

        $collection = $this->getMock('\Magento\Framework\Data\Collection', [], [], '', false);
        $collection->expects($this->atLeastOnce())->method('isLoaded')->will($this->returnValue(true));
        $collection->expects($this->atLeastOnce())->method('clear');
        $collection->expects($this->atLeastOnce())->method('load');

        /** @var \Magento\Backend\Block\Widget\Grid\Extended $block */
        $block = $this->_objectManager->getObject(
            'Magento\Backend\Block\Widget\Grid\Extended',
            ['request' => $request, 'layout' => $layout]
        );

        $block->setCollection($collection);
        $block->getPreparedCollection();
    }
}
