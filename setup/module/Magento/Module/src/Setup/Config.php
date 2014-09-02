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
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Write
     */
    protected $configDirectory;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
        $this->configDirectory = $filesystem->getDirectoryWrite('etc');
    }

    /**
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
     * @param array $data
     * @return $this
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
        print_r($this->configData);
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
     * @param array $source
     * @return array
     */
    protected function convertFromConfigData(array $source = array())
    {
        $result = array();
        $result['db_host'] = isset($source['connection']['host']) && !is_array($source['connection']['host'])
            ? $source['connection']['host'] : '';
        $result['db_name'] = isset($source['connection']['dbName']) && !is_array($source['connection']['dbName'])
            ? $source['connection']['dbName'] : '';
        $result['db_user'] = isset($source['connection']['username']) && !is_array($source['connection']['username'])
            ? $source['connection']['username'] :'';
        $result['db_pass'] = isset($source['connection']['password']) && !is_array($source['connection']['password'])
            ? $source['connection']['password'] : '';
        $result['db_prefix'] = isset($source['db']['table_prefix']) && !is_array($source['db']['table_prefix'])
            ? $source['db']['table_prefix'] : '';
        $result['session_save'] = isset($source['session_save']) && !is_array($source['session_save'])
            ? $source['session_save'] : 'files';
        $result['backend_frontname'] = isset($source['config']['address']['admin']) &&
            !is_array($source['config']['address']['admin'])
            ? $source['config']['address']['admin']
            : '';
        $result['db_model'] = '';
        $result['db_init_statements'] = isset($source['connection']['initStatements'])
            && !is_array($source['connection']['initStatements']) ? $source['connection']['initStatements'] : '';

        $result['admin_username'] = isset($source['admin']['username']) && !is_array($source['admin']['username'])
            ? $source['admin']['username'] : '';
        $result['admin_password'] = isset($source['admin']['password']) && !is_array($source['admin']['password'])
            ? $source['admin']['password'] : '';
        $result['admin_email'] = isset($source['admin']['email']) && !is_array($source['admin']['email'])
            ? $source['admin']['email'] : '';

        return $result;
    }

    /**
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
                    'The table prefix should contain only letters (a-z), numbers (0-9) or underscores (_); the first character should be a letter.'
                );
            }
        }
    }
}
