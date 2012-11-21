<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Eav
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Eav_Model_Resource_Setup_MigrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Name of test module
     */
    const TEST_MODULE_NAME = 'Mage_Eav';

    /**
     * Object manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Config model instance
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_config = $this->_objectManager->get('Mage_Core_Model_Config');
    }

    /**
     * @covers Mage_Eav_Model_Resource_Setup_Migration::_initAliasesMapConfiguration
     *
     * @dataProvider getInitAliasesMapDataProvider
     *
     * @param array $data
     */
    public function testInitAliasesMapConfiguration(array $data)
    {
        /** @var $setupResource Mage_Eav_Model_Resource_Setup_Migration */
        $setupResource = $this->_objectManager->create('Mage_Eav_Model_Resource_Setup_Migration',
            array(
                'resourceName' => Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE,
                'data'         => $data
            )
        );

        if (isset($data['base_dir'])) {
            $expected = $data['base_dir'];
        } else {
            $expected = $this->_config->getModuleDir('', self::TEST_MODULE_NAME);
        }
        $this->assertAttributeEquals($expected, '_baseDir', $setupResource);
    }

    /**
     * Get possible entity types
     *
     * @return array
     */
    public function getInitAliasesMapDataProvider()
    {
        return array(
            'Custom configuration of aliases map file'  => array(
                '$data' => array(
                    'base_dir'         => __DIR__ . '/_files',
                    'path_to_map_file' => 'aliases_to_classes_map.json'
                ),
            ),
            'Default configuration of aliases map file' => array('$data' => array())
        );
    }
}
