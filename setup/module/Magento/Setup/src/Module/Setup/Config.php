<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Module\Setup;

use Magento\Framework\App\Arguments;
use Magento\Framework\App\Arguments\Loader;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Deployment configuration model
 */
class Config
{
    /**#@+
     * Possible variables of the deployment configuration
     */
    const KEY_DATE    = 'date';
    const KEY_DB_HOST = 'db_host';
    const KEY_DB_NAME = 'db_name';
    const KEY_DB_USER = 'db_user';
    const KEY_DB_PASS = 'db_pass';
    const KEY_DB_PREFIX = 'db_prefix';
    const KEY_DB_MODEL = 'db_model';
    const KEY_DB_INIT_STATEMENTS = 'db_init_statements';
    const KEY_SESSION_SAVE = 'session_save';
    const KEY_BACKEND_FRONTNAME = 'backend_frontname';
    const KEY_ENCRYPTION_KEY = 'key';
    /**#@- */

    /**
     * The data values + default values
     *
     * @var string[]
     */
    private $data = [
        self::KEY_DATE => '',
        self::KEY_DB_HOST => '',
        self::KEY_DB_NAME => '',
        self::KEY_DB_USER => '',
        self::KEY_DB_PASS => '',
        self::KEY_DB_PREFIX => '',
        self::KEY_DB_MODEL => 'mysql4',
        self::KEY_DB_INIT_STATEMENTS => 'SET NAMES utf8;',
        self::KEY_SESSION_SAVE => 'files',
        self::KEY_BACKEND_FRONTNAME => 'backend',
        self::KEY_ENCRYPTION_KEY => '',
    ];

    /**
     * Config Directory
     *
     * @var Write
     */
    protected $configDirectory;

    /**
     * @var Arguments
     */
    private $arguments;

    /**
     * Default Constructor
     *
     * @param Filesystem $fileSystem
     * @param Arguments $arguments
     * @param string[] $data
     */
    public function __construct(Filesystem $fileSystem, Arguments $arguments, $data = [])
    {
        $this->configDirectory = $fileSystem->getDirectoryWrite(DirectoryList::CONFIG);

        if ($data) {
            $this->update($data);
        }
        $this->arguments = $arguments;
        $config = $this->arguments->get();
        $data = $this->convertFromConfigData($config);
        $this->update($data);
    }

    /**
     * Retrieve config data
     *
     * @return string[]
     */
    public function getConfigData()
    {
        return $this->data;
    }

    /**
     * Get a value from config data by key
     *
     * @param string $key
     * @return null|string
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Update data
     *
     * @param string[] $data
     * @return void
     */
    public function update($data)
    {
        $new = [];
        foreach (array_keys($this->data) as $key) {
            $new[$key] = isset($data[$key]) ? $data[$key] : $this->data[$key];
        }
        $this->checkData($new);
        $this->data = $new;
    }

    /**
     * Exports data to a deployment configuration file
     *
     * @return void
     * @throws \Exception
     */
    public function saveToFile()
    {
        $contents = $this->configDirectory->readFile(Loader::DEPLOYMENT_CONFIG_FILE_TEMPLATE);
        foreach ($this->data as $index => $value) {
            $contents = str_replace('{{' . $index . '}}', '<![CDATA[' . $value . ']]>', $contents);
        }

        if (preg_match('(\{\{.+?\}\})', $contents, $matches)) {
            throw new \Exception("Some of the keys have not been replaced in the template: {$matches[1]}");
        }

        $this->configDirectory->writeFile(Loader::LOCAL_CONFIG_FILE, $contents);
        $this->configDirectory->changePermissions(Loader::LOCAL_CONFIG_FILE, 0777);
    }

    /**
     * Convert config
     *
     * @param array $source
     * @return array
     */
    private function convertFromConfigData(array $source)
    {
        $result = array();
        $connection = $this->arguments->getConnection(\Magento\Framework\App\Resource\Config::DEFAULT_SETUP_CONNECTION);
        if ($connection) {
            if (isset($connection['host']) && !is_array($connection['host'])) {
                $result[self::KEY_DB_HOST] = $connection['host'];
            }
            if (isset($connection['dbname']) && !is_array($connection['dbname'])) {
                $result[self::KEY_DB_NAME] = $connection['dbname'];
            }
            if (isset($connection['username']) && !is_array($connection['username'])) {
                $result[self::KEY_DB_USER] = $connection['username'];
            }
            if (isset($connection['password']) && !is_array($connection['password'])) {
                $result[self::KEY_DB_PASS] = $connection['password'];
            }
            if (isset($connection['initStatements']) && !is_array($connection['initStatements']) ) {
                $result[self::KEY_DB_INIT_STATEMENTS] = $connection['initStatements'];
            }
        }
        if (isset($source['db.table_prefix']) && !is_array($source['db.table_prefix'])) {
            $result[self::KEY_DB_PREFIX] = $source['db.table_prefix'];
        }
        if (isset($source['session_save']) && !is_array($source['session_save'])) {
            $result[self::KEY_SESSION_SAVE] = $source['session_save'];
        }
        if (isset($source['backend.frontName']) && !is_array($source['backend.frontName'])) {
            $result[self::KEY_BACKEND_FRONTNAME] = $source['backend.frontName'];
        }
        if (isset($source['crypt.key'])) {
            $result[self::KEY_ENCRYPTION_KEY] = $source['crypt.key'];
        }
        if (isset($source['install.date'])) {
            $result[self::KEY_DATE] = $source['install.date'];
        }
        return $result;
    }

    /**
     * Check database connection data
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    private function checkData(array $data)
    {
        if (empty($data[self::KEY_ENCRYPTION_KEY])) {
            throw new \Exception('Encryption key must not be empty.');
        }
        if (empty($data[self::KEY_DATE])) {
            throw new \Exception('Installation date must not be empty.');
        }
        if (empty($data[self::KEY_DB_NAME])) {
            throw new \Exception('The Database Name field cannot be empty.');
        }
        $prefix = $data[self::KEY_DB_PREFIX];
        if ($prefix != '') {
            $prefix = strtolower($prefix);
            if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $prefix)) {
                throw new \Exception(
                    'The table prefix should contain only letters (a-z), numbers (0-9) or underscores (_); '
                    . 'the first character should be a letter.'
                );
            }
        }
    }
}
