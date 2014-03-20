<?php
/**
 * Test configuration of Online Shipping carriers
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

class CarrierConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Structure\Reader
     */
    protected $_reader;

    protected function setUp()
    {
        $moduleReader = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Module\Dir\Reader'
        );
        $schemaFile = $moduleReader->getModuleDir('etc', 'Magento_Backend') . '/system.xsd';
        $this->_reader = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Backend\Model\Config\Structure\Reader',
            array('perFileSchema' => $schemaFile, 'isValidated' => true)
        );
    }

    /**
     * Tests that all source_models used in shipping are valid
     */
    public function testValidateShippingSourceModels()
    {
        $config = $this->_reader->read('adminhtml');

        $carriers = $config['config']['system']['sections']['carriers']['children'];
        foreach ($carriers as $carrier) {
            foreach ($carrier['children'] as $field) {
                if (isset($field['source_model'])) {
                    $model = $field['source_model'];
                    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($model);
                }
            }
        }
    }
}
