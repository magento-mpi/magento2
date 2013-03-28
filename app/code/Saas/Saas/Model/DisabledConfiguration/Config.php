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
     * Disabled sections
     *
     * @var array
     */
    protected $_sections = array();

    /**
     * Disabled groups
     *
     * @var array
     */
    protected $_groups = array();

    /**
     * Disabled fields
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Plain list of disabled nodes
     *
     * @var array
     */
    protected $_plainList = array();

    /**
     * Constructor
     *
     * @param array $plainList
     */
    public function __construct(array $plainList)
    {
        $this->_plainList = $plainList;
        $this->_getStructure($this->_plainList);
    }

    /**
     * Get structured list from plain
     *
     * @param array $plainList
     */
    protected function _getStructure(array $plainList)
    {
        foreach ($plainList as $path) {
            list( , $group, $field) = $this->_parsePath($path);
            if ($field) {
                $this->_fields[$path] = $path;
            } elseif ($group) {
                $this->_groups[$path] = $path;
            } else {
                $this->_sections[$path] = $path;
            }
        }
    }

    /**
     * Get full list of disabled paths
     * Returns array of paths, which can be sections, groups and fields
     *
     * @return array
     */
    public function getDisabledPaths()
    {
        return $this->_plainList;
    }

    /**
     * Get whether passed section is disabled
     *
     * @param $path
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isSectionDisabled($path)
    {
        list($section, $group, ) = $this->_parsePath($path);
        if ($group) {
            throw new InvalidArgumentException("'$path' is incorrect section path");
        }
        return isset($this->_sections[$section]);
    }

    /**
     * Get whether passed group is disabled
     *
     * @param $path
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isGroupDisabled($path)
    {
        list($section, $group, $field) = $this->_parsePath($path);
        if (!$group || $field) {
            throw new InvalidArgumentException("'$path' is incorrect group path");
        }
        return (isset($this->_groups[$path]) || isset($this->_sections[$section]));
    }

    /**
     * Get whether passed field is disabled
     *
     * @param string $path
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isFieldDisabled($path)
    {
        list($section, $group, $field) = $this->_parsePath($path);
        if (!$field) {
            throw new InvalidArgumentException("'$path' is incorrect field path");
        }

        return (
            isset($this->_fields[$path])
            || isset($this->_groups[$section . '/' . $group])
            || isset($this->_sections[$section])
        );
    }

    /**
     * Validate path and split it into parts.
     * It's not a xPath validation, it's more strict
     *
     * @param string $path
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _parsePath($path)
    {
        $regexp = '/^(([a-z\d_])+\/){1,3}$/i';
        if (!preg_match($regexp, $path . '/')) {
            throw new InvalidArgumentException("'$path' is incorrect path");
        }
        $chunks = explode('/', $path);
        return array(
            $chunks[0],
            isset($chunks[1]) ? $chunks[1] : null,
            isset($chunks[2]) ? $chunks[2] : null,
        );
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
