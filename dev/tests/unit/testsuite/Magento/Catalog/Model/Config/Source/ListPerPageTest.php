<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Catalog\Model\Config\Source;

use Magento\TestFramework\Helper\ObjectManager;

class ListPerPageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Config\Source\ListPerPage
     */
    private $model;

    protected function setUp()
    {
        $helper = new ObjectManager($this);
        $this->model = $helper->getObject(
            'Magento\Catalog\Model\Config\Source\ListPerPage',
            ['options' => 'some,test,options']
        );
    }

    public function testToOptionArray()
    {
        $expect = [
            ['value' => 'some', 'label' => 'some'],
            ['value' => 'test', 'label' => 'test'],
            ['value' => 'options', 'label' => 'options'],
        ];

        $this->assertEquals($expect, $this->model->toOptionArray());
    }
}
