<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCard\Model\Source;

use Magento\TestFramework\Helper\ObjectManager;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Model\Source\Status
     */
    protected $model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\GiftCard\Model\Source\Status');
    }

    public function testToOptionArray()
    {
        $expected = [
            [
                'value' => '1',
                'label' => 'Ordered',
            ],
            [
                'value' => '9',
                'label' => 'Invoiced'
            ],

        ];

        $this->assertEquals($expected, $this->model->toOptionArray());
    }
}
