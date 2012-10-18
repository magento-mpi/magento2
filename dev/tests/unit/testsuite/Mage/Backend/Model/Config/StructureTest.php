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

class Mage_Backend_Model_Config_StructureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure
     */
    protected $_model;

    public function setUp()
    {
        $filePath = dirname(__DIR__) . '/_files';

        $this->_model = new Mage_Backend_Model_Config_Structure(array(
            $filePath . '/system_1.xml',
            $filePath . '/system_2.xml'
        ));
    }

    public function testGetSectionsReturnsAllSections()
    {
        $this->_model->getSections();
    }
}
