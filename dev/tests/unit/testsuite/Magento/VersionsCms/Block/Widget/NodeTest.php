<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Block\Widget;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Block\Widget\Node
     */
    protected $node;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $store;

    protected function setUp()
    {
        $this->store = $this->getMockBuilder('Magento\Store\Model\Store')
            ->setMethods(['getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager = $this->getMockForAbstractClass('Magento\Framework\StoreManagerInterface');
        $storeManager->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($this->store));

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->node = $objectManagerHelper->getObject(
            'Magento\VersionsCms\Block\Widget\Node',
            ['storeManager' => $storeManager]
        );
    }

    /**
     * @param int $storeId
     * @param array $data
     * @param string $value
     *
     * @dataProvider getAnchorTextDataProvider
     */
    public function testGetAnchorText($storeId, $data, $value)
    {
        $this->store->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($storeId));

        $this->node->setData($data);
        $this->assertEquals($value, $this->node->getAnchorText());
    }

    public function getAnchorTextDataProvider()
    {
        return [
            [
                $storeId = 1,
                $data = ['anchor_text_1' => 'value_1'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['anchor_text_1' => 'value_1', 'anchor_text_0' => 'value_0'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['anchor_text_1' => 'value_1', 'anchor_text_0' => 'value_0', 'anchor_text' => 'value'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['anchor_text_0' => 'value_0', 'anchor_text' => 'value'],
                $value = 'value_0'
            ],
            [
                $storeId = 1,
                $data = ['anchor_text_2' => 'value_2', 'anchor_text' => 'value'],
                $value = 'value'
            ],
            [
                'storeId' => 1,
                'data' => ['label' => 'some_label'],
                'value' => 'some_label'
            ],
            [
                'storeId' => 1,
                'data' =>
                    [
                        'anchor_text' => null,
                        'anchor_text_1' => null,
                        'label' => 'some_label',
                        'label_1' => 'another_label'],
                'value' => 'another_label'
            ]
        ];
    }
}
