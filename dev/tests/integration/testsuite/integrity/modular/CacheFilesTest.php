<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Modular_CacheFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @dataProvider cacheConfigDataProvider
     */
    public function testCacheConfig($area)
    {
        $validationStateMock = $this->getMock('Magento_Config_ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')->will($this->returnValue(true));

        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        /** @var Mage_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = $objectManager->get('Mage_Core_Model_Config_Modules_Reader');
        $schema = $moduleReader->getModuleDir('etc', 'Mage_Core') . DIRECTORY_SEPARATOR . 'cache.xsd';

        /** @var Mage_Core_Model_Cache_Config_Reader $reader */
        $reader = $objectManager->create(
            'Mage_Core_Model_Cache_Config_Reader',
            array(
                'validationState' => $validationStateMock,
                'schema' => $schema,
                'perFileSchema' => $schema,
            )
        );
        try {
            $reader->read($area);
        } catch (Magento_Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function cacheConfigDataProvider()
    {
        return array(
            'global'    => array('global'),
            'adminhtml' =>array('adminhtml'),
            'frontend'  =>array('frontend'),
        );
    }
}
