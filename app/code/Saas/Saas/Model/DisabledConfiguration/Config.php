<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_DisabledConfiguration
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Manages disabled fields/groups/sections in system configuration
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
     * @return array
     * @throws LengthException
     */
    protected function _getStructure(array $plainList)
    {
        $list = array();
        foreach ($plainList as $path) {
            $this->_validatePath($path);
            $chunks = explode('/', $path);
            switch (count($chunks)) {
                case 1:
                    $this->_sections[$path] = $path;
                    break;
                case 2:
                    $this->_groups[$path] = $path;
                    break;
                case 3:
                    $this->_fields[$path] = $path;
                    break;
            }
        }
        return $list;
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
     * @param $sectionXpath
     * @param Mage_Backend_Model_Config_Structure $configStructure
     * @return bool
     * @throws LogicException
     * @throws LengthException
     */
    public function isSectionDisabled($sectionXpath, Mage_Backend_Model_Config_Structure $configStructure)
    {
        $this->_validatePath($sectionXpath);
        $chunks = explode('/', $sectionXpath);
        if (count($chunks) !== 1) {
            throw new LengthException("'$sectionXpath' is incorrect section path");
        }
        if (isset($this->_sections[$sectionXpath])) {
            return true;
        }

        $section = $configStructure->getElement($sectionXpath);
        if (!($section instanceof Mage_Backend_Model_Config_Structure_Element_CompositeAbstract)) {
            throw new LogicException(
                "'$sectionXpath' should extend Mage_Backend_Model_Config_Structure_Element_CompositeAbstract"
            );
        }

        if ($section->hasChildren()) {
            foreach ($section->getChildren() as $child) {
                if (!$this->isGroupDisabled($child->getPath(), $configStructure)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Get whether passed group is disabled
     *
     * @param $groupXpath
     * @param Mage_Backend_Model_Config_Structure $configStructure
     * @return bool
     * @throws LogicException
     * @throws LengthException
     */
    public function isGroupDisabled($groupXpath, Mage_Backend_Model_Config_Structure $configStructure)
    {
        $this->_validatePath($groupXpath);
        $chunks = explode('/', $groupXpath);
        if (count($chunks) !== 2) {
            throw new LengthException("'$groupXpath' is incorrect group path");
        }
        if (isset($this->_groups[$groupXpath]) || isset($this->_sections[$chunks[0]])) {
            return true;
        }

        $group = $configStructure->getElement($groupXpath);
        if (!($group instanceof Mage_Backend_Model_Config_Structure_Element_CompositeAbstract)) {
            throw new LogicException(
                "'$groupXpath' should extend Mage_Backend_Model_Config_Structure_Element_CompositeAbstract"
            );
        }

        if ($group->hasChildren()) {
            foreach ($group->getChildren() as $child) {
                if (!$this->isFieldDisabled($child->getPath())) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Get whether passed field is disabled
     *
     * @param string $fieldXpath
     * @return bool
     * @throws LengthException
     */
    public function isFieldDisabled($fieldXpath)
    {
        $this->_validatePath($fieldXpath);
        $chunks = explode('/', $fieldXpath);
        if (count($chunks) !== 3) {
            throw new LengthException("'$fieldXpath' is incorrect field path");
        }
        return (
            isset($this->_fields[$fieldXpath])
            || isset($this->_groups[$chunks[0] . '/' . $chunks[1]])
            || isset($this->_sections[$chunks[0]])
        );
    }

    /**
     * Validate path.
     * It's not a xPath validation, it's more strict
     *
     * @param string $path
     * @throws InvalidArgumentException
     */
    protected function _validatePath($path)
    {
        $regexp = '/^(([a-zA-Z0-9_])+\/){1,3}$/';;
        if (!preg_match($regexp, $path . '/')) {
            throw new InvalidArgumentException("'$path' is incorrect path");
        }
    }

    /**
     * Get configuration options mentioned in file
     *
     * @param Magento_Filesystem $filesystem
     * @return mixed
     * @throws LogicException
     */
    public static function getDisabledConfiguration($filesystem)
    {
        $filePath = __DIR__ . '/disabledConfiguration.php';

        if (!$filesystem->has($filePath)) {
            throw new LogicException('File with disabled configuration options was not found');
        }
        return include $filePath;
    }
}
