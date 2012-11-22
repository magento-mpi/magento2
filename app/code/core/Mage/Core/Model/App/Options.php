<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_App_Options
{
    /**@+
     * Application option names
     */
    const OPTION_APP_RUN_CODE            = 'MAGE_RUN_CODE';
    const OPTION_APP_RUN_TYPE            = 'MAGE_RUN_TYPE';
    const OPTION_LOCAL_CONFIG_EXTRA_FILE = 'MAGE_LOCAL_CONFIG';
    /**@-*/

    /**@+
     * Supported application run types
     */
    const APP_RUN_TYPE_STORE    = 'store';
    const APP_RUN_TYPE_GROUP    = 'group';
    const APP_RUN_TYPE_WEBSITE  = 'website';
    /**@-*/

    /**
     * Shorthand for the list of supported application run types
     *
     * @var array
     */
    protected $_supportedRunTypes = array(
        self::APP_RUN_TYPE_STORE, self::APP_RUN_TYPE_GROUP, self::APP_RUN_TYPE_WEBSITE
    );

    /**
     * Store or website code
     *
     * @var string
     */
    protected $_runCode = '';

    /**
     * Run store or run website
     *
     * @var string
     */
    protected $_runType = self::APP_RUN_TYPE_STORE;

    /**
     * Application run options
     *
     * @var array
     */
    protected $_runOptions = array();

    /**
     * Constructor
     *
     * @param array $options Source of option values
     * @throws InvalidArgumentException
     */
    public function __construct(array $options)
    {
        if (isset($options[self::OPTION_APP_RUN_CODE])) {
            $this->_runCode = $options[self::OPTION_APP_RUN_CODE];
        }

        if (isset($options[self::OPTION_APP_RUN_TYPE])) {
            $this->_runType = $options[self::OPTION_APP_RUN_TYPE];
            if (!in_array($this->_runType, $this->_supportedRunTypes)) {
                throw new InvalidArgumentException(sprintf(
                    'Application run type "%s" is not recognized, supported values: "%s".',
                    $this->_runType,
                    implode('", "', $this->_supportedRunTypes)
                ));
            }
        }

        if (!empty($options[self::OPTION_LOCAL_CONFIG_EXTRA_FILE])) {
            $localConfigFile = $options[self::OPTION_LOCAL_CONFIG_EXTRA_FILE];
            $this->_runOptions[Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_FILE] = $localConfigFile;
        }
    }

    /**
     * Retrieve application run code
     *
     * @return string
     */
    public function getRunCode()
    {
        return $this->_runCode;
    }

    /**
     * Retrieve application run type
     *
     * @return string
     */
    public function getRunType()
    {
        return $this->_runType;
    }

    /**
     * Retrieve application run options
     *
     * @return array
     */
    public function getRunOptions()
    {
        return $this->_runOptions;
    }
}
