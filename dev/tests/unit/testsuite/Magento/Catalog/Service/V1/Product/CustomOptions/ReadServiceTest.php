<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionTypeBuilderMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->configMock = $this->getMock('Magento\Catalog\Model\ProductOptions\ConfigInterface');
        $this->optionTypeBuilderMock = $this->getMock(
            'Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionTypeBuilder',
            [],
            [],
            '',
            false
        );

        $this->service = $helper->getObject(
            '\Magento\Catalog\Service\V1\Product\CustomOptions\ReadService',
            [
                'productOptionConfig' => $this->configMock,
                'optionTypeBuilder' => $this->optionTypeBuilderMock,
            ]
        );
    }

    public function testGetTypes()
    {
        $config = [
            [
                'label' => 'group label 1',
                'types' => [
                    [
                        'label' => 'label 1.1',
                        'name' => 'name 1.1',
                        'disabled' => false
                    ],
                ]
            ],
            [
                'label' => 'group label 2',
                'types' => [
                    [
                        'label' => 'label 2.2',
                        'name' => 'name 2.2',
                        'disabled' => true
                    ],
                ]
            ],
        ];

        $this->configMock->expects($this->once())->method('getAll')->will($this->returnValue($config));

        $expectedConfig = [
            'label' => 'label 1.1',
            'code' => 'name 1.1',
            'group' => 'group label 1'
        ];

        $object = $this->getMock('Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionType', [], [], '', false);
        $this->optionTypeBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($expectedConfig)
            ->will($this->returnSelf());

        $this->optionTypeBuilderMock->expects($this->once())->method('create')->will($this->returnValue($object));

        $this->assertEquals([$object], $this->service->getTypes());
    }
}
