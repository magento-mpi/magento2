<?php
/**
 * Class for retrieving disabled fields/groups/sections in system configuration
 *
 * {license_notice}
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_DisabledConfiguration_Config
{
    /**
     * Optimized list for easier searching of disabled nodes
     *
     * @var array
     */
    protected $_optimizedList = array();

    /**
     * Constructor
     *
     * @param array $plainList
     */
    public function __construct(array $plainList)
    {
        $this->_plainList = $plainList;

        foreach ($plainList as $path) {
            $this->_validatePath($path);
            $this->_optimizedList[] = $path . '/';
        }
    }

    /**
     * Get whether passed path is disabled
     *
     * @param $path
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isPathDisabled($path)
    {
        $this->_validatePath($path);

        $path .= '/';
        foreach ($this->_optimizedList as $disabledPath) {
            if (substr($path, 0, strlen($disabledPath)) == $disabledPath) {
                return true;
            }
        }
        return false;
    }

    /**
     * Validate path to have "a/b/c..." notation
     *
     * @param string $path
     * @throws InvalidArgumentException
     */
    protected function _validatePath($path)
    {
        $regexp = '/^(([a-z\d_])+\/)+$/i';
        if (!preg_match($regexp, $path . '/')) {
            throw new InvalidArgumentException("'$path' is incorrect path");
        }
    }

    /**
     * Get list of disabled configuration options from file
     *
     * @return mixed
     */
    public static function getPlainList()
    {
        return include __DIR__ . '/disabled_configuration.php';
    }
}
