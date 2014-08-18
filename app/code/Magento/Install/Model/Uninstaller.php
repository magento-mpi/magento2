<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Install\Model;

use Magento\Framework\App\Filesystem;
use Magento\Framework\Filesystem\FilesystemException;

/**
 * A model for uninstalling Magento application
 */
class Uninstaller
{
    /**
     * A service for cleaning up directories
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * A service for recreating database
     *
     * @var Installer\Db\Mysql4
     */
    private $db;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param Installer\Db\Mysql4 $db
     */
    public function __construct(
        Filesystem $filesystem,
        Installer\Db\Mysql4 $db
    ) {
        $this->filesystem = $filesystem;
        $this->db = $db;
    }

    /**
     * Uninstalls the application
     *
     * @return void
     */
    public function uninstall()
    {
        echo "Starting uninstall\n";
        $this->recreateDatabase();
        echo "File system cleanup:\n";
        $this->deleteDirContents(Filesystem::VAR_DIR);
        $this->deleteDirContents(Filesystem::STATIC_VIEW_DIR);
        $this->deleteLocalXml();
        echo "Uninstall complete.\n";
    }

    /**
     * Deletes the database and creates it again
     *
     * @return void
     */
    private function recreateDatabase()
    {
        $connectionData = $this->db->getConnectionData();
        if (empty($connectionData['dbName'])) {
            echo "No database connection defined - skipping cleanup\n";
        } else {
            echo "Recreating database '{$connectionData['dbName']}'\n";
            $this->db->cleanUpDatabase();
        }
    }

    /**
     * Removes contents of a directory
     *
     * @param string $type
     * @return void
     */
    private function deleteDirContents($type)
    {
        $dir = $this->filesystem->getDirectoryWrite($type);
        $dirPath = $dir->getAbsolutePath();
        foreach ($dir->read() as $path) {
            if (preg_match('/^\./', $path)) {
                continue;
            }
            echo "{$dirPath}{$path}\n";
            try {
                $dir->delete($path);
            } catch (FilesystemException $e) {
                echo "\t{$e->getMessage()}\n";
            }
        }
    }

    /**
     * Removes deployment configuration
     *
     * @return void
     */
    protected function deleteLocalXml()
    {
        $configDir = $this->filesystem->getDirectoryWrite(Filesystem::CONFIG_DIR);
        $localXml = "{$configDir->getAbsolutePath()}local.xml";
        try {
            echo "{$localXml}\n";
            $configDir->delete('local.xml');
        } catch (FilesystemException $e) {
            echo "{$localXml}\n\t{$e->getMessage()}\n";
        }
    }
}
