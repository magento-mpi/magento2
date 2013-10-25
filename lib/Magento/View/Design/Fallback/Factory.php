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
namespace Magento\View\Design\Fallback;

use Magento\App\Dir;
use Magento\View\Design\Fallback\Rule\Composite;
use Magento\View\Design\Fallback\Rule\ModularSwitch;
use Magento\View\Design\Fallback\Rule\RuleInterface;
use Magento\View\Design\Fallback\Rule\Simple;
use Magento\View\Design\Fallback\Rule\Theme;

/**
 * Fallback Factory
 *
 * @package Magento\View
 */
class Factory
{
    /**
     * @var Dir
     */
    protected $dirs;

    /**
     * Constructor
     *
     * @param Dir $dirs
     */
    public function __construct(Dir $dirs)
    {
        $this->dirs = $dirs;
    }

    /**
     * Retrieve newly created fallback rule for locale files, such as CSV translation maps
     *
     * @return RuleInterface
     */
    public function createLocaleFileRule()
    {
        $themesDir = $this->dirs->getDir(Dir::THEMES);
        return new Theme(
            new Simple("$themesDir/<area>/<theme_path>/i18n/<locale>")
        );
    }

    /**
     * Retrieve newly created fallback rule for dynamic view files, such as layouts and templates
     *
     * @return RuleInterface
     */
    public function createFileRule()
    {
        $themesDir = $this->dirs->getDir(Dir::THEMES);
        $modulesDir = $this->dirs->getDir(Dir::MODULES);
        return new ModularSwitch(
            new Theme(
                new Simple(
                    "$themesDir/<area>/<theme_path>"
                )
            ),
            new Composite(
                array(
                    new Theme(
                        new Simple(
                            "$themesDir/<area>/<theme_path>/<namespace>_<module>"
                        )
                    ),
                    new Simple(
                        "$modulesDir/<namespace>/<module>/view/<area>"
                    ),
                )
            )
        );
    }

    /**
     * Retrieve newly created fallback rule for static view files, such as CSS, JavaScript, images, etc.
     *
     * @return RuleInterface
     */
    public function createViewFileRule()
    {
        $themesDir = $this->dirs->getDir(Dir::THEMES);
        $modulesDir = $this->dirs->getDir(Dir::MODULES);
        $pubLibDir = $this->dirs->getDir(Dir::PUB_LIB);
        return new ModularSwitch(
            new Composite(
                array(
                    new Theme(
                        new Composite(
                            array(
                                new Simple(
                                    "$themesDir/<area>/<theme_path>/i18n/<locale>",
                                    array('locale')
                                ),
                                new Simple(
                                    "$themesDir/<area>/<theme_path>"
                                ),
                            )
                        )
                    ),
                    new Simple($pubLibDir),
                )
            ),
            new Composite(
                array(
                    new Theme(
                        new Composite(
                            array(
                                new Simple(
                                    "$themesDir/<area>/<theme_path>/i18n/<locale>/<namespace>_<module>",
                                    array('locale')
                                ),
                                new Simple(
                                    "$themesDir/<area>/<theme_path>/<namespace>_<module>"
                                ),
                            )
                        )
                    ),
                    new Simple(
                        "$modulesDir/<namespace>/<module>/view/<area>/i18n/<locale>",
                        array('locale')
                    ),
                    new Simple(
                        "$modulesDir/<namespace>/<module>/view/<area>"
                    ),
                )
            )
        );
    }
}
