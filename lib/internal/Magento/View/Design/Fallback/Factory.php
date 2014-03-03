<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Fallback;

use Magento\App\Filesystem;
use Magento\View\Design\Fallback\Rule\Composite;
use Magento\View\Design\Fallback\Rule\ModularSwitch;
use Magento\View\Design\Fallback\Rule\RuleInterface;
use Magento\View\Design\Fallback\Rule\Simple;
use Magento\View\Design\Fallback\Rule\Theme;

/**
 * Fallback Factory
 *
 * Factory that produces all sorts of fallback rules
 */
class Factory
{
    /**
     * File system
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Retrieve newly created fallback rule for locale files, such as CSV translation maps
     *
     * @return RuleInterface
     */
    public function createLocaleFileRule()
    {
        $themesDir = $this->filesystem->getPath(Filesystem::THEMES_DIR);
        return new Theme(
            new Simple("$themesDir/<area>/<theme_path>")
        );
    }

    /**
     * Retrieve newly created fallback rule for template files
     *
     * @return RuleInterface
     */
    public function createTemplateFileRule()
    {
        $themesDir = $this->filesystem->getPath(Filesystem::THEMES_DIR);
        $modulesDir = $this->filesystem->getPath(Filesystem::MODULES_DIR);
        return new ModularSwitch(
            new Theme(
                new Simple("$themesDir/<area>/<theme_path>/templates")
            ),
            new Composite(
                array(
                    new Theme(new Simple("$themesDir/<area>/<theme_path>/<namespace>_<module>/templates")),
                    new Simple("$modulesDir/<namespace>/<module>/view/<area>/templates"),
                )
            )
        );
    }

    /**
     * Retrieve newly created fallback rule for dynamic view files
     *
     * @return RuleInterface
     */
    public function createFileRule()
    {
        $themesDir = $this->filesystem->getPath(Filesystem::THEMES_DIR);
        $modulesDir = $this->filesystem->getPath(Filesystem::MODULES_DIR);
        return new ModularSwitch(
            new Theme(new Simple("$themesDir/<area>/<theme_path>")),
            new Composite(
                array(
                    new Theme(new Simple("$themesDir/<area>/<theme_path>/<namespace>_<module>")),
                    new Simple("$modulesDir/<namespace>/<module>/view/<area>"),
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
        $themesDir = $this->filesystem->getPath(Filesystem::THEMES_DIR);
        $modulesDir = $this->filesystem->getPath(Filesystem::MODULES_DIR);
        $libDir = $this->filesystem->getPath(Filesystem::LIB_WEB);
        return new ModularSwitch(
            new Composite(
                array(
                    new Theme(
                        new Composite(
                            array(
                                new Simple("$themesDir/<area>/<theme_path>/web/i18n/<locale>", array('locale')),
                                new Simple("$themesDir/<area>/<theme_path>/web"),
                            )
                        )
                    ),
                    new Simple($libDir),
                )
            ),
            new Composite(
                array(
                    new Theme(
                        new Composite(
                            array(
                                new Simple(
                                    "$themesDir/<area>/<theme_path>/<namespace>_<module>/web/i18n/<locale>",
                                    array('locale')
                                ),
                                new Simple("$themesDir/<area>/<theme_path>/<namespace>_<module>/web"),
                            )
                        )
                    ),
                    new Simple(
                        "$modulesDir/<namespace>/<module>/view/<area>/web/i18n/<locale>",
                        array('locale')
                    ),
                    new Simple("$modulesDir/<namespace>/<module>/view/<area>/web"),
                )
            )
        );
    }
}
