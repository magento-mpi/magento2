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
        foreach ($plainList as $path) {
            $this->_optimizedList[] = $path . '/';
        }
    }

    /**
     * Get whether passed path is disabled
     *
     * @param string $path
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isPathDisabled($path)
    {
        $path .= '/';
        foreach ($this->_optimizedList as $disabledPath) {
            if (substr($path, 0, strlen($disabledPath)) == $disabledPath) {
                return true;
            }
        }
        return false;
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
