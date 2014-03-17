<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\ImportExport\Fixture;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIteratorInterface()
    {
        $pattern = array(
            'id' => '%s',
            'name' => 'Static',
            'calculated' => function ($index) {
                return $index * 10;
            }
        );
        $model = new \Magento\TestFramework\ImportExport\Fixture\Generator($pattern, 2);
        $rows = array();
        foreach ($model as $row) {
            $rows[] = $row;
        }
        $this->assertEquals(
            array(
                array('id' => '1', 'name' => 'Static', 'calculated' => 10),
                array('id' => '2', 'name' => 'Static', 'calculated' => 20)
            ),
            $rows
        );
    }
}
