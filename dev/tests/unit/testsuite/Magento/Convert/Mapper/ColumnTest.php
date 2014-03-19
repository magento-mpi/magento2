<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Convert\Mapper;

class ColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Column
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new Column();
    }

    public function testGetSetDataWithoutMapSetting()
    {
        $input = [
            'key2' => ['key21' => 'value21', 'key22' => 'value22', 'key23' => 'value23'],
            'key3' => ['key31' => 'value31', 'key32' => 'value32'],
            'key4' => ['key41' => 'value41']
        ];

        $this->model->setData($input);
        $this->assertTrue($this->model->validateDataGrid());
        $this->model->map();
        $this->checkArray($this->model->getData(), $input);
    }

    public function testGetSetDataWithMapSetting()
    {
        $input = [
            'key2' => ['key21' => 'value21', 'key22' => 'value22', 'key23' => 'value23'],
            'key3' => ['key31' => 'value31', 'key32' => 'value32'],
            'key4' => ['key41' => 'value41']
        ];

        $output = [
            'key2' => ['key21' => 'value21', 'key22' => 'value22', 'new_key23' => 'value23'],
            'key3' => ['key31' => 'value31', 'new_key32' => 'value32'],
            'key4' => ['key41' => 'value41']
        ];

        $mapKeys = ['key23' => 'new_key23', 'key32' => 'new_key32'];

        $this->model->setData($input);
        $this->assertTrue($this->model->validateDataGrid());
        $this->model->setVar($mapKeys);
        $this->model->map();
        $this->checkArray($this->model->getData(), $output);
    }

    protected function checkArray($data, $expectedData = array())
    {
        foreach ($expectedData as $key => $value) {
            $this->assertArrayHasKey($key, $data);
            if (is_array($value)) {
                $this->checkArray($data[$key], $value);
            } else {
                $this->assertEquals($data[$key], $value);
            }
        }
    }
}
