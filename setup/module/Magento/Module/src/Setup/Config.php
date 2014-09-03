<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Module\Setup;

use Magento\Filesystem\Directory\Write;
use Magento\Filesystem\Filesystem;

/**
 * Config installer
 */
class Config
{
    const TMP_INSTALL_DATE_VALUE = 'd-d-d-d-d';

    const TMP_ENCRYPT_KEY_VALUE = 'k-k-k-k-k';

    /**
     * Path to local configuration file
     *
     * @var string
     */
    protected $localConfigFile = 'local.xml';

    /**
     * @var array
     */
    protected $configData = array();

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
     * Default Constructor
     *
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
        $this->configDirectory = $filesystem->getDirectoryWrite('etc');
    }

    /**
     * Sets Configuration Data
     *
     * @param array $data
     * @return $this
     */
    public function setConfigData(array $data)
    {
        if (is_array($data)) {
            $this->addConfigData($this->convert($data));
        }
        return $this;
    }

    /**
     * Add Configuration Data
     *
     * @param array $data
     */
    public function addConfigData(array $data)
    {
        $this->configData = array_merge($this->configData, $data);
    }

    /**
     * Retrieve config data
     *
     * @return array
     */
    public function getConfigData()
    {
        return $this->configData;
    }

    /**
     * Generate installation data and record them into local.xml using local.xml.template
     *
     * @return void
     */
    public function install()
    {
        $this->configData['date'] = self::TMP_INSTALL_DATE_VALUE;
        $this->configData['key'] = self::TMP_ENCRYPT_KEY_VALUE;

        $this->checkData();

        $contents = $this->configDirectory->readFile('local.xml.template');
        foreach ($this->configData as $index => $value) {
            $contents = str_replace('{{' . $index . '}}', '<![CDATA[' . $value . ']]>', $contents);
        }

        $this->configDirectory->writeFile($this->localConfigFile, $contents, LOCK_EX);
        $this->configDirectory->changePermissions($this->localConfigFile, 0777);
    }

    /**
     * Loads Configuration from Local Config File
     * @return $this
     */
    public function loadFromConfigFile()
    {
        $xmlData = $this->configDirectory->readFile($this->localConfigFile);
        $xmlObj = @simplexml_load_string($xmlData, NULL, LIBXML_NOCDATA);
        $xmlConfig = json_decode(json_encode((array)$xmlObj), true);
        $config = $this->convertFromConfigData((array)$xmlConfig);
        $this->addConfigData($config);
        return $this;
    }

    /**
     * Convert config
     *
     * @param array $source
     * @return array
     */
    protected function convertFromConfigData(array $source = [])
    {
        $result = array();
        if (isset($source['connection']['host']) && !is_array($source['connection']['host'])) {
            $result['db_host'] = $source['connection']['host'];
        }
        if (isset($source['connection']['dbName']) && !is_array($source['connection']['dbName'])) {
            $result['db_name'] = $source['connection']['dbName'];
        }
        if (isset($source['connection']['username']) && !is_array($source['connection']['username'])) {
            $result['db_user'] = $source['connection']['username'];
        }
        if (isset($source['connection']['password']) && !is_array($source['connection']['password'])) {
            $result['db_pass'] = $source['connection']['password'];
        }
        if (isset($source['db']['table_prefix']) && !is_array($source['db']['table_prefix'])) {
            $result['db_prefix'] = $source['db']['table_prefix'];
        }
        if (isset($source['session_save']) && !is_array($source['session_save'])) {
            $result['session_save'] = $source['session_save'];
        }
        if (isset($source['config']['address']['admin']) && !is_array($source['config']['address']['admin'])) {
            $result['backend_frontname'] = $source['config']['address']['admin'];
        }
        if (isset($source['connection']['initStatements']) && !is_array($source['connection']['initStatements']) ) {
            $result['db_init_statements'] = $source['connection']['initStatements'];
        }
        if (isset($source['admin']['username']) && !is_array($source['admin']['username'])) {
            $result['admin_username'] = $source['admin']['username'];
        }
        if (isset($source['admin']['password']) && !is_array($source['admin']['password']) ) {
            $result['admin_password'] = $source['admin']['password'];
        }
        if (isset($source['admin']['email']) && !is_array($source['admin']['email']) ) {
            $result['admin_email'] = $source['admin']['email'];
        }
        return $result;
    }

    /**
     * Replace Install Date
     *
     * @param string $date
     * @return $this
     */
    public function replaceTmpInstallDate($date = 'now')
    {
        $stamp = strtotime((string)$date);
        $localXml = $this->configDirectory->readFile($this->localConfigFile);
        $localXml = str_replace(self::TMP_INSTALL_DATE_VALUE, date('r', $stamp), $localXml);
        $this->configDirectory->writeFile($this->localConfigFile, $localXml, LOCK_EX);

        return $this;
    }

    /**
     * Replace Encryption Key
     *
     * @param string $key
     * @return $this
     */
    public function replaceTmpEncryptKey($key)
    {
        $localXml = $this->configDirectory->readFile($this->localConfigFile);
        $localXml = str_replace(self::TMP_ENCRYPT_KEY_VALUE, $key, $localXml);
        $this->configDirectory->writeFile($this->localConfigFile, $localXml, LOCK_EX);

        return $this;
    }

    /**
     * Convert config
     *
     * @param array $source
     * @return array
     */
    protected function convert(array $source = array())
    {
        $result = array();
        $result['db_host'] = isset($source['db']['host']) ? $source['db']['host'] : '';
        $result['db_name'] = isset($source['db']['name']) ? $source['db']['name'] : '';
        $result['db_user'] = isset($source['db']['user']) ? $source['db']['user'] :'';
        $result['db_pass'] = isset($source['db']['password']) ? $source['db']['password'] : '';
        $result['db_prefix'] = isset($source['db']['tablePrefix']) ? $source['db']['tablePrefix'] : '';
        $result['session_save'] = 'files';
        $result['backend_frontname'] = isset($source['config']['address']['admin'])
            ? $source['config']['address']['admin']
            : '';
        $result['db_model'] = '';
        $result['db_init_statements'] = '';

        $result['admin_username'] = isset($source['admin']['username']) ? $source['admin']['username'] : '';
        $result['admin_password'] = isset($source['admin']['password']) ? $source['admin']['password'] : '';
        $result['admin_email'] = isset($source['admin']['email']) ? $source['admin']['email'] : '';
        return $result;
    }

    /**
     * Check database connection data
     *
     * @return void
     * @throws \Exception
     */
    protected function checkData()
    {
        if (!isset($this->configData['db_name']) || empty($this->configData['db_name'])) {
            throw new \Exception('The Database Name field cannot be empty.');
        }
        //make all table prefix to lower letter
        if ($this->configData['db_prefix'] != '') {
            $this->configData['db_prefix'] = strtolower($this->configData['db_prefix']);
        }
        //check table prefix
        if ($this->configData['db_prefix'] != '') {
            if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $this->configData['db_prefix'])) {
                throw new \Exception(
                    'The table prefix should contain only letters (a-z), numbers (0-9) or underscores (_); '.
                    'the first character should be a letter.'
                );
            }
        }
    }
}
