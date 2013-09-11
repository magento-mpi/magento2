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
     * @var \Magento\Backend\Model\Config\Structure\Mapper\Dependencies
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new \Magento\Backend\Model\Config\Structure\Mapper\Dependencies(
            new \Magento\Backend\Model\Config\Structure\Mapper\Helper\RelativePathConverter()
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
