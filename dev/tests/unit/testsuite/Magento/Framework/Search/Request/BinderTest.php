<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

use Magento\TestFramework\Helper\ObjectManager;

class BinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\Request\Binder
     */
    private $binder;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->binder = $helper->getObject('Magento\Framework\Search\Request\Binder');
    }

    public function testBind()
    {
        $requestData = [
            'dimensions' => ['scope' => ['value' => '$sss$']],
            'queries' => ['query' => ['value' => '$query$']],
            'filters' => ['filter' => ['from' => '$from$', 'to' => '$to$', 'value' => '$filter$']],
            'from' => 0,
            'size' => 15
        ];
        $bindData = [
            'dimensions' => ['scope' => 'default'],
            'placeholder' => [
                '$query$' => 'match_query',
                '$from$' => 'filter_from',
                '$to$' => 'filter_to',
                '$filter$' => 'filter_value'
            ],
            'from' => 1,
            'size' => 10
        ];
        $expectedResult = [
            'dimensions' => ['scope' => ['value' => 'default']],
            'queries' => ['query' => ['value' => 'match_query']],
            'filters' => ['filter' => ['from' => 'filter_from', 'to' => 'filter_to', 'value' => 'filter_value']],
            'from' => 1,
            'size' => 10
        ];

        $result = $this->binder->bind($requestData, $bindData);

        $this->assertEquals($result, $expectedResult);
    }
}
