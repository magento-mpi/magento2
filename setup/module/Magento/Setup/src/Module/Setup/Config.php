<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Module\Setup;

use Magento\Filesystem\Directory\Write;
use Magento\Filesystem\Filesystem;
use Magento\Framework\Math\Random;

/**
 * Deployment configuration model
 */
class Config
{
    const TMP_INSTALL_DATE_VALUE = 'd-d-d-d-d';

    const TMP_ENCRYPT_KEY_VALUE = 'k-k-k-k-k';

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
     * Path to deployment config file
     */
    const DEPLOYMENT_CONFIG_FILE = 'local.xml';

    /**
     * @var array
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
     * Filesystem
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Config Directory
     *
     * @var Write
     */
    protected $configDirectory;

    /**
     * Random Generator
     *
     * @var Random
     */
    protected $random;

    /**
     * Default Constructor
     *
     * @param Filesystem $filesystem
     * @param Random $random
     * @param array $data
     */
    public function __construct(
        Filesystem $filesystem,
        Random $random,
        $data = []
    ) {
        $this->filesystem = $filesystem;
        $this->configDirectory = $filesystem->getDirectoryWrite('etc');
        $this->random = $random;
        if ($data) {
            $this->update($data);
        }
    }

    /**
     * Retrieve config data
     *
     * @return array
     */
    public function getConfigData()
    {
        return $this->data;
    }

    /**
     * Get a value from config data by key
     *
     * @param string $key
     * @return null|mixed
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Update data
     *
     * @param array $data
     * @return void
     */
    public function update($data)
    {
        foreach (array_keys($this->data) as $key) {
            if (isset($data[$key])) {
                $this->data[$key] = $data[$key];
            }
        }
    }

    /**
     * Generate installation data and record them into local.xml using local.xml.template
     *
     * @return string Installation Key
     */
    public function install()
    {
        $data = $this->data;
        $data[self::KEY_DATE] = date('r');
        if (empty($data[self::KEY_ENCRYPTION_KEY])) {
            $data[self::KEY_ENCRYPTION_KEY] = md5($this->random->getRandomString(10));
        }
        $this->checkData($data);
        $contents = $this->configDirectory->readFile('local.xml.template');
        foreach ($data as $index => $value) {
            $contents = str_replace('{{' . $index . '}}', '<![CDATA[' . $value . ']]>', $contents);
        }
        if (preg_match('(\{\{[\w\d\_\-]\}\})', $contents, $matches)) {
            throw new \Exception("Some of the keys have not been replaced in the template: {$matches[1]}");
        }

        $this->configDirectory->writeFile(self::DEPLOYMENT_CONFIG_FILE, $contents, LOCK_EX);
        $this->configDirectory->changePermissions(self::DEPLOYMENT_CONFIG_FILE, 0777);
        return $data[self::KEY_ENCRYPTION_KEY];
    }

    /**
     * Loads configuration the deployment configuration file
     *
     * @return void
     */
    public function loadFromFile()
    {
        $xmlData = $this->configDirectory->readFile(self::DEPLOYMENT_CONFIG_FILE);
        $xmlObj = @simplexml_load_string($xmlData, NULL, LIBXML_NOCDATA);
        $xmlConfig = json_decode(json_encode((array)$xmlObj), true);
        $data = $this->convertFromConfigData((array)$xmlConfig);
        $this->update($data);
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
        if (isset($source['connection']['host']) && !is_array($source['connection']['host'])) {
            $result[self::KEY_DB_HOST] = $source['connection']['host'];
        }
        if (isset($source['connection']['dbName']) && !is_array($source['connection']['dbName'])) {
            $result[self::KEY_DB_NAME] = $source['connection']['dbName'];
        }
        if (isset($source['connection']['username']) && !is_array($source['connection']['username'])) {
            $result[self::KEY_DB_USER] = $source['connection']['username'];
        }
        if (isset($source['connection']['password']) && !is_array($source['connection']['password'])) {
            $result[self::KEY_DB_PASS] = $source['connection']['password'];
        }
        if (isset($source['db']['table_prefix']) && !is_array($source['db']['table_prefix'])) {
            $result[self::KEY_DB_PREFIX] = $source['db']['table_prefix'];
        }
        if (isset($source['session_save']) && !is_array($source['session_save'])) {
            $result[self::KEY_SESSION_SAVE] = $source['session_save'];
        }
        if (isset($source['config']['address']['admin']) && !is_array($source['config']['address']['admin'])) {
            $result[self::KEY_BACKEND_FRONTNAME] = $source['config']['address']['admin'];
        }
        if (isset($source['connection']['initStatements']) && !is_array($source['connection']['initStatements']) ) {
            $result[self::KEY_DB_INIT_STATEMENTS] = $source['connection']['initStatements'];
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
        if (!isset($data[self::KEY_DB_NAME]) || empty($data[self::KEY_DB_NAME])) {
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
