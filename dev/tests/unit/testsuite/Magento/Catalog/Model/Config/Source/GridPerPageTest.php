<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source;

use Magento\TestFramework\Helper\ObjectManager;

class GridPerPageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Config\Source\GridPerPage
     */
    private $model;

    protected function setUp()
    {
        $helper = new ObjectManager($this);
        $this->model = $helper->getObject(
            'Magento\Catalog\Model\Config\Source\GridPerPage',
            ['perPageValues' => 'some,values']
        );
    }

    public function testToOptionalArray()
    {
        $expect = [
            ['value' => 'some', 'label' => 'some'],
            ['value' => 'values', 'label' => 'values'],
        ];

        $this->assertEquals($expect, $this->model->toOptionArray());
    }
} 