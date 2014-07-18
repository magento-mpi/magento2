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
    public function setConfigData($data)
    {
        if (is_array($data)) {
            $this->configData = $this->convert($data);
        }
        return $this;
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

        $contents = $this->configDirectory->readFile('local.xml.template');
        foreach ($this->configData as $index => $value) {
            $contents = str_replace('{{' . $index . '}}', '<![CDATA[' . $value . ']]>', $contents);
        }

        $this->configDirectory->writeFile($this->localConfigFile, $contents, LOCK_EX);
        $this->configDirectory->changePermissions($this->localConfigFile, 0777);
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
        $result['backend_frontname'] = isset($source['config']['address']['admin']) ? $source['config']['address']['admin'] : '';
        $result['db_model'] = '';
        $result['db_init_statements'] = '';

        return $result;
    }
}
