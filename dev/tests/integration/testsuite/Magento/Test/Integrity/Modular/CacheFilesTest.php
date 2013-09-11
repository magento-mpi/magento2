<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_CacheFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @dataProvider cacheConfigDataProvider
     */
    public function testCacheConfig($area)
    {
        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')->will($this->returnValue(true));

        /** @var \Magento\Core\Model\Config\Modules\Reader $moduleReader */
        $moduleReader = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Config\Modules\Reader');
        $schema = $moduleReader->getModuleDir('etc', 'Magento_Core') . DIRECTORY_SEPARATOR . 'cache.xsd';

        /** @var \Magento\Core\Model\Cache\Config\Reader $reader */
        $reader = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
            '\Magento\Core\Model\Cache\Config\Reader',
            array(
                'validationState' => $validationStateMock,
                'schema' => $schema,
                'perFileSchema' => $schema,
            )
        );
        try {
            $reader->read($area);
        } catch (\Magento\Exception $exception) {
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
