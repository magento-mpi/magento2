<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Connect_Frontend
{
    /**
     * Silent flag. If set no output is produced to view.
     * Should be used in derived classes.
     *
     * @var boolean
     */
    protected $_silent = false;

    /**
     * Capture mode. If set command output should be collected
     * by derived class impplementation
     *
     * @var boolean
     */
    protected $_capture = false;

    /**
     * push/pop variable for capture
     *
     * @var array
     */
    protected $_captureSaved = array();

    /**
     * push/pop variable for silent
     *
     * @var array
     */
    protected $_silentSaved = array();

    /**
     * Errors list
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Add error to errors list
     *
     * @param mixed $data
     * @return null
     */
    public function addError($data)
    {
        $this->_errors[] = $data;
    }

    /**
     * Get errors, clear errors list with first param
     *
     * @param boolean $clear
     * @return array
     */
    public function getErrors($clear = true)
    {
        if(!$clear) {
            return $this->_errors;
        }
        $out = $this->_errors;
        $this->clearErrors();
        return $out;
    }

    /**
     * Clear errors array
     *
     * @return null
     */
    public function clearErrors()
    {
        $this->_errors = array();
    }

    /**
     * Are there any errros?
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return count($this->_errors) != 0;
    }

    /**
     * Error processing
     * @param string $command
     * @param string $message
     * @return null
     */
    public function doError($command, $message)
    {
        $this->addError(array($command, $message));
    }

    /**
     * Save capture state
     *
     * @return Mage_Connect_Frontend
     */
    public function pushCapture()
    {
        array_push($this->_captureSaved, $this->_capture);
        return $this;
    }

    /**
     * Restore capture state
     *
     * @return Mage_Connect_Frontend
     */
    public function popCapture()
    {
        $this->_capture = array_pop($this->_captureSaved);
        return $this;
    }

    /**
     * Set capture mode
     *
     * @param boolean $arg true by default
     * @return Mage_Connect_Frontend
     */
    public function setCapture($arg = true)
    {
        $this->_capture = $arg;
        return $this;
    }

    /**
     * Getter for capture mode
     *
     * @return boolean
     */
    public function isCapture()
    {
        return $this->_capture;
    }

    /**
     * Log stub
     *
     * @param $msg
     * @return
     */
    public function log($msg)
    {

    }

    /**
     * Ouptut method
     *
     * @param array $data
     * @return null
     */
    public function output($data)
    {

    }

    /**
     * Get instance of derived class
     *
     * @param $class CLI for example will produce Mage_Connect_Frontend_CLI
     * @return object
     */
    public static function getInstance($class)
    {
        $class = __CLASS__."_".$class;
        return new $class();
    }

    /**
     * Get output if capture mode set
     * Clear prevoius if needed
     *
     * @param boolean $clearPrevious
     * @return mixed
     */
    public function getOutput($clearPrevious = true)
    {

    }

    /**
     * Save silent mode
     *
     * @return Mage_Connect_Frontend
     */
    public function pushSilent()
    {
        array_push($this->_silentSaved, $this->_silent);
        return $this;
    }

    /**
     * Restore silent mode
     *
     * @return Mage_Connect_Frontend
     */
    public function popSilent()
    {
        $this->_silent = array_pop($this->_silentSaved);
        return $this;
    }

    /**
     * Set silent mode
     *
     * @param boolean $value
     * @return Mage_Connect_Frontend
     */
    public function setSilent($value = true)
    {
        $this->_silent = (boolean) $value;
        return $this;
    }

    /**
     * Is silent mode?
     *
     * @return boolean
     */
    public function isSilent()
    {
        return (boolean) $this->_silent;
    }

    /**
    * Method for ask client about rewrite all files.
    *
    * @param $string
    */
    public function confirm($string)
    {

    }
}

