<?php
/**
 * Config sections list. Used to cache/read config sections separately.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Sections
{
    /**
     * Instructions for spitting config cache
     * array(
     *      $sectionName => $recursionLevel
     * )
     * Recursion level provides availability to cache subnodes separatly
     *
     * @var array
     */
    protected $_sections = array(
        'admin'     => 0,
        'adminhtml' => 0,
        'crontab'   => 0,
        'install'   => 0,
        'stores'    => 1,
        'websites'  => 0
    );

    /**
     * Retrieve sections
     *
     * @return array
     */
    public function getSections()
    {
        return $this->_sections;
    }

    /**
     * Retrieve section cache key by path
     *
     * @param string $path
     * @return bool|string
     */
    public function getKey($path)
    {
        $pathParts = explode('/', $path);
        if (!array_key_exists($pathParts[0], $this->_sections)) {
            return false;
        }
        return implode('_', array_slice($pathParts, 0, $this->_sections[$pathParts[0]] + 1));
    }
}
