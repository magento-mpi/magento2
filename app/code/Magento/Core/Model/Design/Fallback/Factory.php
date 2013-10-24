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
namespace Magento\Core\Model\Design\Fallback;

class Factory
{
    /**
     * @var \Magento\App\Dir
     */
    private $_dirs;

    /**
     * Constructor
     *
     * @param \Magento\App\Dir $dirs
     */
    public function __construct(\Magento\App\Dir $dirs)
    {
        $this->_dirs = $dirs;
    }

    /**
     * Retrieve newly created fallback rule for locale files, such as CSV translation maps
     *
     * @return \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    public function createLocaleFileRule()
    {
        $themesDir = $this->_dirs->getDir(\Magento\App\Dir::THEMES);
        return new \Magento\Core\Model\Design\Fallback\Rule\Theme(
            new \Magento\Core\Model\Design\Fallback\Rule\Simple("$themesDir/<area>/<theme_path>/i18n/<locale>")
        );
    }

    /**
     * Retrieve newly created fallback rule for dynamic view files, such as layouts and templates
     *
     * @return \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    public function createFileRule()
    {
        $themesDir = $this->_dirs->getDir(\Magento\App\Dir::THEMES);
        $modulesDir = $this->_dirs->getDir(\Magento\App\Dir::MODULES);
        return new \Magento\Core\Model\Design\Fallback\Rule\ModularSwitch(
            new \Magento\Core\Model\Design\Fallback\Rule\Theme(
                new \Magento\Core\Model\Design\Fallback\Rule\Simple(
                    "$themesDir/<area>/<theme_path>"
                )
            ),
            new \Magento\Core\Model\Design\Fallback\Rule\Composite(array(
                new \Magento\Core\Model\Design\Fallback\Rule\Theme(
                    new \Magento\Core\Model\Design\Fallback\Rule\Simple(
                        "$themesDir/<area>/<theme_path>/<namespace>_<module>"
                    )
                ),
                new \Magento\Core\Model\Design\Fallback\Rule\Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>"
                ),
            ))
        );
    }

    /**
     * Retrieve newly created fallback rule for static view files, such as CSS, JavaScript, images, etc.
     *
     * @return \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    public function createViewFileRule()
    {
        $themesDir = $this->_dirs->getDir(\Magento\App\Dir::THEMES);
        $modulesDir = $this->_dirs->getDir(\Magento\App\Dir::MODULES);
        $pubLibDir = $this->_dirs->getDir(\Magento\App\Dir::PUB_LIB);
        return new \Magento\Core\Model\Design\Fallback\Rule\ModularSwitch(
            new \Magento\Core\Model\Design\Fallback\Rule\Composite(array(
                new \Magento\Core\Model\Design\Fallback\Rule\Theme(
                    new \Magento\Core\Model\Design\Fallback\Rule\Composite(array(
                        new \Magento\Core\Model\Design\Fallback\Rule\Simple(
                            "$themesDir/<area>/<theme_path>/i18n/<locale>", array('locale')
                        ),
                        new \Magento\Core\Model\Design\Fallback\Rule\Simple(
                            "$themesDir/<area>/<theme_path>"
                        ),
                    ))
                ),
                new \Magento\Core\Model\Design\Fallback\Rule\Simple($pubLibDir),
            )),
            new \Magento\Core\Model\Design\Fallback\Rule\Composite(array(
                new \Magento\Core\Model\Design\Fallback\Rule\Theme(
                    new \Magento\Core\Model\Design\Fallback\Rule\Composite(array(
                        new \Magento\Core\Model\Design\Fallback\Rule\Simple(
                            "$themesDir/<area>/<theme_path>/i18n/<locale>/<namespace>_<module>", array('locale')
                        ),
                        new \Magento\Core\Model\Design\Fallback\Rule\Simple(
                            "$themesDir/<area>/<theme_path>/<namespace>_<module>"
                        ),
                    ))
                ),
                new \Magento\Core\Model\Design\Fallback\Rule\Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>/i18n/<locale>", array('locale')
                ),
                new \Magento\Core\Model\Design\Fallback\Rule\Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>"
                ),
            ))
        );
    }
}
