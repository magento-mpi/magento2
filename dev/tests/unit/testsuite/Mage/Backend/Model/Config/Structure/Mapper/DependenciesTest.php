<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Mapper_DependenciesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Mapper_Dependencies
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Backend_Model_Config_Structure_Mapper_Dependencies();
    }

    public function testMap()
    {
        $data = require_once (realpath(dirname(__FILE__) . '/../../../') . '/_files/dependencies_data.php');
        $expected = require_once (realpath(dirname(__FILE__) . '/../../../') . '/_files/dependencies_mapped.php');

        $actual = $this->_model->map($data);
        $this->assertEquals($expected, $actual);
    }
}
