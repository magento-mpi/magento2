<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout;

use Magento\View\Design\ThemeInterface;

/**
 * Layout file in the file system with context of its identity
 */
class File
{
    /**
     * File name
     *
     * @var string
     */
    protected $filename;

    /**
     * Module
     *
     * @var string
     */
    protected $module;

    /**
     * Theme
     *
     * @var ThemeInterface
     */
    protected $theme;

    /**
     * Identifier
     *
     * @var string
     */
    protected $identifier;

    /**
     * Constructor
     *
     * @param string $filename
     * @param string $module
     * @param ThemeInterface $theme
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
     * @return ThemeInterface|null
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

    /**
     * Calculate unique identifier for a layout file
     *
     * @return string
     */
    public function getFileIdentifier()
    {
        if (null === $this->identifier) {
            $theme = ($this->getTheme() ? 'theme:' . $this->theme->getFullPath() : 'base');
            $this->identifier = $theme . '|module:' . $this->getModule() . '|file:' . $this->getName();
        }
        return $this->identifier;
    }
}
