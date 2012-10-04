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

class Magento_ImportExport_Fixture_GeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testIteratorInterface()
    {
        $model = new Magento_ImportExport_Fixture_Generator(array('id' => '%s', 'name' => 'Static'), 2);
        $rows = array();
        foreach ($model as $row) {
            $rows[] = $row;
        }
        $this->assertEquals(array(
            array('id' => '1', 'name' => 'Static'),
            array('id' => '2', 'name' => 'Static'),
        ), $rows);
    }
}
