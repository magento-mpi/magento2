<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * The class for keeping scenario configuration
 */
class Magento_Performance_Config_Scenario
{
    /**#@+
     * Common scenario arguments
     */
    const ARG_USERS           = 'users';
    const ARG_LOOPS           = 'loops';
    const ARG_HOST            = 'host';
    const ARG_PATH            = 'path';
    const ARG_BASEDIR         = 'basedir';
    const ARG_ADMIN_USERNAME  = 'admin_username';
    const ARG_ADMIN_PASSWORD  = 'admin_password';
    const ARG_ADMIN_FRONTNAME = 'admin_frontname';
    /**#@-*/

    /**
     * Scenario title
     *
     * @var string
     */
    protected $_title;

    /**
     * File path
     *
     * @var string
     */
    protected $_file;

    /**
     * Framework settings
     *
     * @var string
     */
    protected $_settings;

    /**
     * Arguments, passed to scenario
     *
     * @var array
     */
    protected $_arguments;

    /**
     * Fixtures, needed to be applied before scenario execution
     *
     * @var array
     */
    protected $_fixtures;

    /**
     * Imports raw config data, parses it and stores in the object
     *
     * @param string $title
     * @param array|string $scenarioConfig
     * @param array $defaultConfig
     * @param string $baseDir
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws Magento_Exception
     */
    public function __construct($title, $scenarioConfig, array $defaultConfig, $fixedArguments, $baseDir)
    {
        // Title
        if (!strlen($title)) {
            throw new InvalidArgumentException("Scenario must have a title");
        }

        // File
        if (!is_array($scenarioConfig)) { // Scenarios without parameters can be represented just by file paths
            $scenarioConfig = array('file' => $scenarioConfig);
        }
        if (!isset($scenarioConfig['file'])) {
            throw new InvalidArgumentException("Scenario '{$title}' must have a file declared");
        }
        $file = realpath($baseDir . DIRECTORY_SEPARATOR . $scenarioConfig['file']);
        if (!file_exists($file)) {
            throw new Magento_Exception("File path for scenario '{$title}' doesn't exist in $baseDir");
        }

        // Compose config, using global config
        $scenarioConfig = $this->_getCompletedArray($scenarioConfig, $defaultConfig, array('fixtures'));

        // Fixtures
        $scenarioConfig['fixtures'] = $this->_expandFixtures($scenarioConfig, $baseDir);

        // Settings
        if (!isset($scenarioConfig['settings'])) {
            $scenarioConfig['settings'] = array();
        }

        // Arguments
        $arguments = $this->_composeArguments($scenarioConfig, $fixedArguments);

        // Import resulting data
        $this->_title = $title;
        $this->_file = $file;
        $this->_settings = $scenarioConfig['settings'];
        $this->_arguments = $arguments;
        $this->_fixtures = $scenarioConfig['fixtures'];
    }

    /**
     * Retrieve new array composed from an input array by supplementing missing values
     *
     * @param array $input
     * @param array $supplement
     * @param array $override
     * @return array
     */
    protected function _getCompletedArray(array $input, array $supplement, array $merge)
    {
        foreach ($supplement as $key => $sourceVal) {
            if (in_array($key, $merge)) {
                $input[$key] = array_merge($input[$key], $sourceVal);
            } else {
                if (!empty($input[$key])) {
                    $input[$key] += $sourceVal;
                } else {
                    $input[$key] = $sourceVal;
                }
            }
        }
        return $input;
    }

    /**
     * Process fixture file names from scenario config and compose array of their full file paths
     *
     * @param array $scenarioConfig
     * @param string $baseDir
     * @return array
     * @throws InvalidArgumentException
     * @throws Magento_Exception
     */
    protected function _expandFixtures(array $scenarioConfig, $baseDir)
    {
        if (!isset($scenarioConfig['fixtures'])) {
            return array();
        }

        if (!is_array($scenarioConfig['fixtures'])) {
            throw new InvalidArgumentException(
                "Scenario 'fixtures' option must be an array, not a value: '{$scenarioConfig['fixtures']}'"
            );
        }

        $result = array();
        foreach ($scenarioConfig['fixtures'] as $fixtureName) {
            $fixtureFile = realpath($baseDir . DIRECTORY_SEPARATOR . $fixtureName);
            if (!file_exists($fixtureFile)) {
                throw new Magento_Exception("Fixture '$fixtureName' doesn't exist in $baseDir");
            }
            $result[] = $fixtureFile;
        }

        return $result;
    }

    /**
     * Processes arguments and ensures that 'loops' and 'users' are defined
     *
     * @param array $scenarioConfig
     * @param array $fixedArguments
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _composeArguments(array $scenarioConfig, array $fixedArguments)
    {
        $arguments = isset($scenarioConfig['arguments']) ? $scenarioConfig['arguments'] : array();
        $arguments = array_merge($arguments, $fixedArguments);
        $arguments += array(self::ARG_USERS => 1, self::ARG_LOOPS => 1);
        foreach (array(self::ARG_USERS, self::ARG_LOOPS) as $argName) {
            if (!is_int($arguments[$argName]) || $arguments[$argName] < 1) {
                throw new InvalidArgumentException("Scenario argument '$argName' must be a positive integer.");
            }
        }
        return $arguments;
    }


    /**
     * Retrieve title of the scenario
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Retrieve file of the scenario
     *
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Retrieve framework settings of the scenario
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->_settings;
    }

    /**
     * Retrieve arguments of the scenario
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->_arguments;
    }

    /**
     * Retrieve fixtures of the scenario
     *
     * @return array
     */
    public function getFixtures()
    {
        return $this->_fixtures;
    }
}
