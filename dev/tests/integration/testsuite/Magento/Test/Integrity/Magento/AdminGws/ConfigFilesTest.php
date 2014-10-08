<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\AdminGws;

use Magento\Framework\App\Filesystem\DirectoryList;

class ConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Config\Reader
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $filesystem \Magento\Framework\App\Filesystem */
        $filesystem = $objectManager->get('Magento\Framework\App\Filesystem');
        $modulesDirectory = $filesystem->getDirectoryRead(DirectoryList::MODULES);
        $fileIteratorFactory = $objectManager->get('Magento\Framework\Config\FileIteratorFactory');
        $xmlFiles = $fileIteratorFactory->create(
            $modulesDirectory,
            $modulesDirectory->search('/*/*/etc/{*/admingws.xml,admingws.xml}')
        );

        $validationStateMock = $this->getMock('Magento\Framework\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')->will($this->returnValue(true));
        $fileResolverMock = $this->getMock('Magento\Framework\Config\FileResolverInterface');
        $fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($xmlFiles));
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $objectManager->create(
            'Magento\AdminGws\Model\Config\Reader',
            array('fileResolver' => $fileResolverMock, 'validationState' => $validationStateMock)
        );
    }

    public function testAdminGwsXmlFiles()
    {
        $this->_model->read('global');
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testClassInstances()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\AdminGws\Model\Config $config */
        $config = $objectManager->get('Magento\AdminGws\Model\Config');
        $errors = [];
        foreach ($config->get('callbacks') as $groupName => $callbacks) {
            if ($groupName == 'controller_predispatch') {
                //skip for controllers
                continue;
            }
            foreach (array_keys($callbacks) as $targetClass) {
                if (!class_exists($targetClass)) {
                    $errors[] = 'Class [' . $targetClass . '] does not exists. Group ' . $groupName;
                }
            }
        }

        $processors = $config->get('processors');
        foreach ($processors as $processor) {
            if (!is_subclass_of($processor, '\Magento\AdminGws\Model\CallbackProcessorInterface')) {
                $errors[] = 'Processor [' . $processor . '] does not implements CallbackProcessorInterface';
            }
        }
        $message = implode(PHP_EOL, $errors);
        $this->assertEmpty($errors, PHP_EOL . 'Found ' . count($errors) . ' error(s):' . PHP_EOL . $message);
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testProcessorInterfaces()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\AdminGws\Model\Config $config */
        $config = $objectManager->get('Magento\AdminGws\Model\Config');
        $processors = $config->get('processors');
        $errors = [];
        foreach ($config->get('callbacks') as $groupName => $callbacks) {
            foreach ($callbacks as $callback) {
                $processor = $processors[$groupName];
                if (!method_exists($processor, $callback)) {
                    $errors[] = 'Invalid callback [' . $processor . '::' . $callback . ']. Method does not exists';
                }
            }
        }
        $message = implode(PHP_EOL, $errors);
        $this->assertEmpty($errors, PHP_EOL . 'Found ' . count($errors) . ' error(s):' . PHP_EOL . $message);
    }
}
