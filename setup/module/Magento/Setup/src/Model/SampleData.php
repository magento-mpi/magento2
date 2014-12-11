<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Setup\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class SampleData
 */
class SampleData
{
    const INSTALLER_PATH = 'dev/tools/Magento/Tools/SampleData/install.php';

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
        return is_file($this->directoryList->getRoot() . DIRECTORY_SEPARATOR . self::INSTALLER_PATH);
    }

    /**
     * Returns command to be executed for Sample Data installation
     *
     * @param \ArrayObject|array $request
     * @return string
     */
    public function getRunCommand($request)
    {
        $userName = isset($request[AdminAccount::KEY_USERNAME]) ? $request[AdminAccount::KEY_USERNAME] : '';
        return $command = ' -f ' . $this->directoryList->getRoot() . DIRECTORY_SEPARATOR . self::INSTALLER_PATH .
            ' -- --admin_username=' . $userName . ' --bootstrap=%s';
    }
}
