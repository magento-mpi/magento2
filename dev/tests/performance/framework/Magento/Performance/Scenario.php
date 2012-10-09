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
class Magento_Performance_Scenario
{
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
     * Arguments, passed to scenario
     *
     * @var array
     */
    protected $_arguments;

    /**
     * Constructor
     *
     * @param string $title
     * @param string $file
     * @param array $arguments
     */
    public function __construct($title, $file, array $arguments)
    {
        $this->_title = $title;
        $this->_file = $file;
        $this->_arguments = $arguments;
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
     * Retrieve arguments of the scenario
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->_arguments;
    }
}
