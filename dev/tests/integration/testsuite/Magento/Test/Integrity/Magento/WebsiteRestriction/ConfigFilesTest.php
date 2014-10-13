<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\WebsiteRestriction;

use Magento\Framework\App\Filesystem\DirectoryList;

class ConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\WebsiteRestriction\Model\Config\Reader
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $filesystem \Magento\Framework\Filesystem */
        $filesystem = $objectManager->get('Magento\Framework\Filesystem');
        $modulesDirectory = $filesystem->getDirectoryRead(DirectoryList::MODULES);
        $fileIteratorFactory = $objectManager->get('Magento\Framework\Config\FileIteratorFactory');
        $xmlFiles = $fileIteratorFactory->create(
            $modulesDirectory,
            $modulesDirectory->search('/*/*/etc/{*/webrestrictions.xml,webrestrictions.xml}')
        );

        $fileResolverMock = $this->getMock('Magento\Framework\Config\FileResolverInterface');
        $fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($xmlFiles));
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $validationStateMock = $this->getMock('Magento\Framework\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')->will($this->returnValue(true));
        $this->_model = $objectManager->create(
            'Magento\WebsiteRestriction\Model\Config\Reader',
            array('fileResolver' => $fileResolverMock, 'validationState' => $validationStateMock)
        );
    }

    public function testWebsiteRestrictionXmlFiles()
    {
        $this->_model->read('global');
    }
}
