<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Class SampleData
 */
class SampleData
{
    const INSTALLER_PATH = '/dev/tools/Magento/Tools/SampleData/install.php';

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @param DirectoryList $directoryList
     */
    public function __construct(DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    /**
     * Check if Sample Data was deployed
     *
     * @return bool
     * @throws \Magento\Framework\Exception
     */
    public function isDeployed()
    {
        return is_file($this->directoryList->getRoot() . self::INSTALLER_PATH);
    }

    /**
     * Returns command to be executed for Sample Data installation
     *
     * @return string
     */
    public function getRunCommand()
    {
        $phpFinder = new PhpExecutableFinder();
        $phpPath = $phpFinder->find();
        $command = $phpPath . ' -f ' . $this->directoryList->getRoot() . self::INSTALLER_PATH;
        return $command;
    }
}
