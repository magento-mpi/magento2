<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Catalog\Model\Product\Attribute\Source;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Catalog\Model\Product\Attribute\Source\Layout */
    protected $layoutModel;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Theme\Model\Layout\Source\Layout|\PHPUnit_Framework_MockObject_MockObject */
    protected $layoutSourceModel;

    protected function setUp()
    {
        $this->layoutSourceModel = $this->getMock(
            'Magento\Theme\Model\Layout\Source\Layout',
            array(
                'toOptionArray'
            ),
            array(),
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->layoutModel = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Product\Attribute\Source\Layout',
            array(
                'pageSourceLayout' => $this->layoutSourceModel
            )
        );
    }

    public function testGetAllOptions()
    {
        $expectedOptions = array(
            '0' => array('value' => '', 'label' => 'No layout updates'),
            '1' => array('value' => 'option_value', 'label' => 'option_label')
        );
        $this->layoutSourceModel->expects($this->once())->method('toOptionArray')
            ->will($this->returnValue(array('0' => $expectedOptions['1'])));
        $layoutOptions = $this->layoutModel->getAllOptions();
        $this->assertEquals($expectedOptions, $layoutOptions);
    }
}
