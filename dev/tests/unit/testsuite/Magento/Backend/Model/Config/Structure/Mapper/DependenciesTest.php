<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Mapper_DependenciesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Config_Structure_Mapper_Dependencies
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Magento_Backend_Model_Config_Structure_Mapper_Dependencies(
            new Magento_Backend_Model_Config_Structure_Mapper_Helper_RelativePathConverter()
        );
    }

    public function testMap()
    {
        $data = require_once (realpath(dirname(__FILE__) . '/../../../') . '/_files/dependencies_data.php');
        $expected = require_once (realpath(dirname(__FILE__) . '/../../../') . '/_files/dependencies_mapped.php');

        $actual = $this->_model->map($data);
        $this->assertEquals($expected, $actual);
    }
}
