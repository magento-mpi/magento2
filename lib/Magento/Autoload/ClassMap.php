<?php
/**
 * An autoloader that uses a class map to load files
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
class Magento_Autoload_ClassMap
{
    /**
     * Absolute path to base directory that will be prepended as prefix to the included files
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Map of class name to file (relative to the base directory)
     *
     * array(
     *     'Class_Name' => 'relative/path/to/Class/Name.php',
     * )
     *
     * @var array
     */
    protected $_map = array();

    /**
     * Set base directory absolute path
     *
     * @param string $baseDir
     * @throws InvalidArgumentException
     */
    public function __construct($baseDir)
    {
        $this->_baseDir = realpath($baseDir);
        if (!$this->_baseDir || !is_dir($this->_baseDir)) {
            throw new InvalidArgumentException("Specified path is not a valid directory: '{$baseDir}'");
        }
    }

    public function autoload($class)
    {
        if (isset($this->_map[$class])) {
            $file = $this->_baseDir . DIRECTORY_SEPARATOR . $this->_map[$class];
            if (file_exists($file)) {
                include $file;
            }
        }
    }

    /**
     * Add classes files declaration to the map. New map will override existing values if such was defined before.
     *
     * @param array $map
     * @return Magento_Autoload_ClassMap
     */
    public function addMap(array $map)
    {
        $this->_map = array_merge($this->_map, $map);
        return $this;
    }
}
