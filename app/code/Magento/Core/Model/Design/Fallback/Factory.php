<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory that produces all sorts of fallback rules
 */
class Magento_Core_Model_Design_Fallback_Factory
{
    /**
     * @var Magento_Core_Model_Dir
     */
    private $_dirs;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Dir $dirs
     */
    public function __construct(Magento_Core_Model_Dir $dirs)
    {
        $this->_dirs = $dirs;
    }

    /**
     * Retrieve newly created fallback rule for locale files, such as CSV translation maps
     *
     * @return Magento_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    public function createLocaleFileRule()
    {
        $themesDir = $this->_dirs->getDir(Magento_Core_Model_Dir::THEMES);
        return new Magento_Core_Model_Design_Fallback_Rule_Theme(
            new Magento_Core_Model_Design_Fallback_Rule_Simple("$themesDir/<area>/<theme_path>/i18n/<locale>")
        );
    }

    /**
     * Retrieve newly created fallback rule for dynamic view files, such as layouts and templates
     *
     * @return Magento_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    public function createFileRule()
    {
        $themesDir = $this->_dirs->getDir(Magento_Core_Model_Dir::THEMES);
        $modulesDir = $this->_dirs->getDir(Magento_Core_Model_Dir::MODULES);
        return new Magento_Core_Model_Design_Fallback_Rule_ModularSwitch(
            new Magento_Core_Model_Design_Fallback_Rule_Theme(
                new Magento_Core_Model_Design_Fallback_Rule_Simple(
                    "$themesDir/<area>/<theme_path>"
                )
            ),
            new Magento_Core_Model_Design_Fallback_Rule_Composite(array(
                new Magento_Core_Model_Design_Fallback_Rule_Theme(
                    new Magento_Core_Model_Design_Fallback_Rule_Simple(
                        "$themesDir/<area>/<theme_path>/<namespace>_<module>"
                    )
                ),
                new Magento_Core_Model_Design_Fallback_Rule_Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>"
                ),
            ))
        );
    }

    /**
     * Retrieve newly created fallback rule for static view files, such as CSS, JavaScript, images, etc.
     *
     * @return Magento_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    public function createViewFileRule()
    {
        $themesDir = $this->_dirs->getDir(Magento_Core_Model_Dir::THEMES);
        $modulesDir = $this->_dirs->getDir(Magento_Core_Model_Dir::MODULES);
        $pubLibDir = $this->_dirs->getDir(Magento_Core_Model_Dir::PUB_LIB);
        return new Magento_Core_Model_Design_Fallback_Rule_ModularSwitch(
            new Magento_Core_Model_Design_Fallback_Rule_Composite(array(
                new Magento_Core_Model_Design_Fallback_Rule_Theme(
                    new Magento_Core_Model_Design_Fallback_Rule_Composite(array(
                        new Magento_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>/i18n/<locale>", array('locale')
                        ),
                        new Magento_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>"
                        ),
                    ))
                ),
                new Magento_Core_Model_Design_Fallback_Rule_Simple($pubLibDir),
            )),
            new Magento_Core_Model_Design_Fallback_Rule_Composite(array(
                new Magento_Core_Model_Design_Fallback_Rule_Theme(
                    new Magento_Core_Model_Design_Fallback_Rule_Composite(array(
                        new Magento_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>/i18n/<locale>/<namespace>_<module>", array('locale')
                        ),
                        new Magento_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>/<namespace>_<module>"
                        ),
                    ))
                ),
                new Magento_Core_Model_Design_Fallback_Rule_Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>/i18n/<locale>", array('locale')
                ),
                new Magento_Core_Model_Design_Fallback_Rule_Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>"
                ),
            ))
        );
    }
}
