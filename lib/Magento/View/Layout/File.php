<?php
/**
 * Layout file in the file system with context of its identity
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout;

use Magento\Core\Model\ThemeInterface;

class File
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $module;

    /**
     * @var \Magento\Core\Model\ThemeInterface
     */
    private $theme;

    /**
     * @param string $filename
     * @param string $module
     * @param \Magento\Core\Model\ThemeInterface $theme
     */
    public function __construct($filename, $module, ThemeInterface $theme = null)
    {
        $this->filename = $filename;
        $this->module = $module;
        $this->theme = $theme;
    }

    /**
     * Retrieve full filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Retrieve name of a file without a directory path
     *
     * @return string
     */
    public function getName()
    {
        return basename($this->filename);
    }

    /**
     * Retrieve fully-qualified name of a module a file belongs to
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Retrieve instance of a theme a file belongs to
     *
     * @return \Magento\Core\Model\ThemeInterface|null
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Whether file is a base one
     *
     * @return bool
     */
    public function isBase()
    {
        return is_null($this->theme);
    }
}
