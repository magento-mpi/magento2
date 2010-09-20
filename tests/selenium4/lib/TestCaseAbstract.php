<?php
/**
 * Abstract test class derived from PHPUnit_Extensions_SeleniumTestCase
 *
 * @author Magento Inc.
 */
abstract class TestCaseAbstract extends PHPUnit_Extensions_SeleniumTestCase
{
    /**
     * Instances of initiated framework models
     *
     * @var array
     */
    protected $_modelInstances = array();

    /**
     * Local variables area
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Current UI Map path namespace
     *
     * @var string
     */
    protected $_uiNamespace = '';

    /**
     * Constructor.
     * Overridden to generate a testcase-related ID
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '', array $browser = array())
    {
        parent::__construct($name, $data, $dataName, $browser);
        $this->testId = strtoupper(get_class($this));
    }

    /**
     * Add an error to the stack
     *
     * @param string $error
     */
    function setVerificationErrors($error)
    {
        array_push($this->verificationErrors, $error);
        $this->printError($error);
    }

    /**
     * Fetch an Xpath to access a certain UI element
     *
     * @param string $elem
     * @param string | array $arg
     * @return string
     */
    public function getUiElement($elem, $arg = null)
    {
        // Applying namespace if appropriate
        if ('/' !== $elem[0]) {
            $elem = trim($this->_uiNamespace, '/') . '/' . $elem;
        }

        $element = Core::getEnvMap($elem);

        if (null === $element) {
            $this->printError('MAP({$elem}): No element found for key: <' . $elem .'>');
            return $element;
        }

        if (is_array($arg)) {
            $str = vsprintf($element, $arg);
        } elseif (null !== $arg) {
            $str = sprintf($element, $arg);
        } else {
            $str = $element;
        }

        $this->printDebug("MAP({$elem}): {$str}");

        return $str;
    }

    /**
     * Set UI Map namespace
     *
     * @param string $namespace
     * @return TestcaseAbstract
     */
    public function setUiNamespace($namespace = '')
    {
        $this->_uiNamespace = (string) $namespace;

        if ($this->_uiNamespace && !Core::getEnvMap($this->_uiNamespace)) {
            $this->printError("MAP({$this->_uiNamespace}): Namespace does not exist");
        }

        return $this;
    }

    /**
     * Print an info message according to the current debug level
     *
     * @param string $line
     */
    public function printInfo($line)
    {
        Core::debug($line, Core::DEBUG_LEVEL_INFO);
    }

    /**
     * Print a debug message according to the current debug level
     *
     * @param string $line
     */
    public function printDebug($line)
    {
        Core::debug($line, Core::DEBUG_LEVEL_DEBUG);
    }

    /**
     * Print an error message according to the current debug level
     *
     * @param string $line
     */
    public function printError($line)
    {
        Core::debug($line, Core::DEBUG_LEVEL_ERROR);
    }

    /**
     * Wait of appearance of html element with Xpath during timeforwait sec
     *
     * @param string $xpath
     * @param int $timeforwait
     * @return boolean
     */
    public function waitForElement($xpath, $timeforwait) {
        for ($second = 0; ; $second ++) {
            if ($second >= $timeforwait) {
                $this->printDebug('Element could not be found: ' . $xpath);
                return false;
            }

            try {
                if ($this->isElementPresent($xpath)) {
                    return true;
                }
            } catch (Exception $e) {
            }
            sleep(1);
        }
        return false;
    }

    /**
     * Initialize a framework model
     */
    public function getModel($modelName)
    {
        if (!isset($this->_modelInstances[$modelName])) {
            $modelClassName = 'Model_' . str_replace(' ', '_', ucwords(str_replace('/', ' ', $modelName)));
            $this->_modelInstances[$modelName] = new $modelClassName($this);
            $this->_modelInstances[$modelName]->loadConfigData();
        }

        return $this->_modelInstances[$modelName];
    }

    /**
     * Retrieve an environment unique stamp
     *
     * @return string 
     */
    public function getStamp()
    {
        return Core::getStamp();
    }

    /**
     * Simple overloading mechanism
     * Get variable implementation
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return isset($this->_data[$name]) ? $this->_data[$name] : null;
    }

    /**
     * Simple overloading mechanism
     * Set variable implementation
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }

    /**
     * Simple overloading mechanism
     * Is Set variable implementation
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        return isset($this->_data[$name]);
    }

    /**
     * Simple overloading mechanism
     * Unset variable implementation
     *
     * @param string $name
     */
    public function __unset($name) {
        unset($this->_data[$name]);
    }
}

