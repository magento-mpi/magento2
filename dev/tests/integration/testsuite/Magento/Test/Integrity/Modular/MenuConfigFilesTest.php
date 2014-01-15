<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Modular;

class MenuConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Menu\Config\Reader
     */
    protected $_model;

    protected function setUp()
    {
        $moduleReader = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Module\Dir\Reader');
        $schemaFile = $moduleReader->getModuleDir('etc', 'Magento_Backend') . '/menu.xsd';
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\Menu\Config\Reader',
            array(
                'perFileSchema' => $schemaFile,
                'isValidated' => true,
            )
        );
    }

    public function testValidateMenuFiles()
    {
        $this->_model->read('adminhtml');
    }
}
