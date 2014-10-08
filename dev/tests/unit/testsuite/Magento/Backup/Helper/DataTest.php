<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Helper;

use Magento\Framework\App\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\MaintenanceMode;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backup\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    public function setUp()
    {
        $this->filesystem = $this->getMockBuilder('Magento\Framework\App\Filesystem')->disableOriginalConstructor()
            ->getMock();

        $this->helper = (new \Magento\TestFramework\Helper\ObjectManager($this))
            ->getObject('Magento\Backup\Helper\Data', [
                'filesystem' => $this->filesystem,
            ]);
    }

    public function testGetBackupIgnorePaths()
    {
        $this->filesystem->expects($this->any())->method('getPath')
            ->will($this->returnValueMap([
                [MaintenanceMode::FLAG_DIR, MaintenanceMode::FLAG_DIR],
                [DirectoryList::SESSION, DirectoryList::SESSION],
                [DirectoryList::CACHE, DirectoryList::CACHE],
                [DirectoryList::LOG, DirectoryList::LOG],
                [DirectoryList::VAR_DIR, DirectoryList::VAR_DIR],
            ]));

        $this->assertEquals(
            [
                '.git',
                '.svn',
                'var/' . MaintenanceMode::FLAG_FILENAME,
                DirectoryList::SESSION,
                DirectoryList::CACHE,
                DirectoryList::LOG,
                DirectoryList::VAR_DIR . '/full_page_cache',
                DirectoryList::VAR_DIR . '/locks',
                DirectoryList::VAR_DIR . '/report',
            ],
            $this->helper->getBackupIgnorePaths()
        );
    }

    public function testGetRollbackIgnorePaths()
    {
        $this->filesystem->expects($this->any())->method('getPath')
            ->will($this->returnValueMap([
                [MaintenanceMode::FLAG_DIR, MaintenanceMode::FLAG_DIR],
                [DirectoryList::SESSION, DirectoryList::SESSION],
                [DirectoryList::ROOT, DirectoryList::ROOT],
                [DirectoryList::LOG, DirectoryList::LOG],
                [DirectoryList::VAR_DIR, DirectoryList::VAR_DIR],
            ]));

        $this->assertEquals(
            [
                '.svn',
                '.git',
                'var/' . MaintenanceMode::FLAG_FILENAME,
                DirectoryList::SESSION,
                DirectoryList::LOG,
                DirectoryList::VAR_DIR . '/locks',
                DirectoryList::VAR_DIR . '/report',
                DirectoryList::ROOT . '/errors',
                DirectoryList::ROOT . '/index.php',
            ],
            $this->helper->getRollbackIgnorePaths()
        );
    }
}
