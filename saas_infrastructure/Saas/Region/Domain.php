<?php
class Saas_Region_Domain
{
    /**
     * Current TMT Config Data
     *
     * @var null|SimpleXMLElement
     */
    protected static $_config = null;

    /**
     * Get Current TMT Config Data
     *
     * @static
     * @throws Exception
     * @return null|SimpleXMLElement
     */
    protected static function _getConfig()
    {
        if (is_null(self::$_config)) {
            $allConfig = self::_loadConfigData();
            foreach ($allConfig->instances->children() as $node) {
                if ((string)$node->name === TMT_INSTANCE_NAME) {
                    self::$_config = $node;
                    break;
                }
            }
            if (is_null(self::$_config)) {
                throw new Exception('Unable to load configuration for current TMT instance');
            }
        }
        return self::$_config;
    }

    /**
     * Load XML-data (from configuration file)
     *
     * @static
     * @throws Exception
     * @return SimpleXMLElement
     */
    protected static function _loadConfigData()
    {
        $configFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'tmt_instance.xml';
        if (is_readable($configFilePath)) {
            $config = new SimpleXMLElement(file_get_contents($configFilePath));
        } else {
            throw new Exception('Unable to load TMT instance configuration data');
        }
        return $config;
    }

    /**
     * Returns true if it is default domain
     *
     * @param  string $domain
     * @return boolean
     */
    public static function isOurDomain($domain)
    {
        $config = self::_getConfig();
        $baseDomain = preg_replace('/^(www\.)?[^\.]+\./', '', $domain);
        foreach ($config->regions->children() as $regionNode) {
            if ($domain == (string)$regionNode->domain
                || $baseDomain == (string)$regionNode->domain
            ) {
                return true;
            }
        }
        return false;
    }
}
