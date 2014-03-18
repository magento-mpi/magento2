<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Backend\Image;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Backend\Image\Adapter
     */
    protected $_model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\Config\Backend\Image\Adapter');
    }

    /**
     * @expectedException \Magento\Model\Exception
     * expectedExceptionMessage  The specified image adapter cannot be used because of some missed dependencies.
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExceptionSave()
    {
        $this->_model->setValue('wrong')->save();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCorrectSave()
    {
        $this->_model->setValue(\Magento\Image\Adapter\AdapterInterface::ADAPTER_GD2)->save();
    }
}
