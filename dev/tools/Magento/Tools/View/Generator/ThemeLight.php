<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    view
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\View\Generator;

use Magento\View\Design\ThemeInterface;

/**
 * Lightweight theme that implements minimal required interface
 */
class ThemeLight extends \Magento\Object implements ThemeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getArea()
    {
        return $this->getData('area');
    }

    /**
     * {@inheritdoc}
     */
    public function getThemePath()
    {
        return $this->getData('theme_path');
    }

    /**
     * {@inheritdoc}
     */
    public function getFullPath()
    {
        return $this->getArea() . ThemeInterface::PATH_SEPARATOR . $this->getThemePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getParentTheme()
    {
        return $this->getData('parent_theme');
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return (string)$this->getData('code');
    }

    /**
     * {@inheritdoc}
     */
    public function isPhysical()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getInheritedThemes()
    {
        return array();
    }
}
