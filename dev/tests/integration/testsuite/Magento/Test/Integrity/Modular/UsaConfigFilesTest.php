<?php
namespace Magento\Test\Integrity\Modular;

/**
 * Test configuration of Usa shipping carriers
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class UsaConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Structure\Reader
     */
    protected $_model;

    protected function setUp()
    {
        $moduleReader = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Config\Modules\Reader');
        $schemaFile = $moduleReader->getModuleDir('etc', 'Magento_Backend') . DIRECTORY_SEPARATOR . 'system.xsd';
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(
                'Magento\Backend\Model\Config\Structure\Reader',
                array(
                    'perFileSchema' => $schemaFile,
                    'isValidated' => true,
                )
            );
    }

    /**
     * Tests that all source_models used in shipping are valid
     */
    public function testValidateShippingSourceModels()
    {
        $config = $this->_model->read('adminhtml');

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
