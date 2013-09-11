<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * An ancestor class for integrity tests
 */
abstract class Magento_TestFramework_TestCase_IntegrityAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * Cached index of enabled modules
     *
     * @var array
     */
    protected $_enabledModules = null;

    /**
     * Returns array of enabled modules
     *
     * @return array
     */
    protected function _getEnabledModules()
    {
        if ($this->_enabledModules === null) {
            /** @var $helper Magento_TestFramework_Helper_Config */
            $helper = Magento_TestFramework_Helper_Factory::getHelper('config');
            $enabledModules = $helper->getEnabledModules();
            $this->_enabledModules = array_combine($enabledModules, $enabledModules);
        }
        return $this->_enabledModules;
    }

    /**
     * Checks resource file declaration - whether it is for disabled module (e.g. 'Disabled_Module::file.ext').
     *
     * @param string $file
     * @return bool
     */
    protected function _isFileForDisabledModule($file)
    {
        $enabledModules = $this->_getEnabledModules();
        if (preg_match('/^(.*)::/', $file, $matches)) {
            $module = $matches[1];
            if (!isset($enabledModules[$module])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns flat array of themes currently located in system
     *
     * @return \Magento\Core\Model\Theme[]
     */
    protected function _getDesignThemes()
    {
        $themeItems = array();
        /** @var $themeCollection \Magento\Core\Model\Theme\Collection */
        $themeCollection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Resource\Theme\Collection');
        /** @var $theme \Magento\Core\Model\Theme */
        foreach ($themeCollection as $theme) {
            $themeItems[$theme->getId()] = $theme;
        }
        return $themeItems;
    }
}
