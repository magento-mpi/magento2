<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Theme;

/**
 * Data model for themes
 *
 * @method \Magento\Framework\View\Design\ThemeInterface setArea(string $area)
 */
class Data extends \Magento\Core\Model\Theme
{
    /**
     * {@inheritdoc}
     */
    public function getArea()
    {
        return $this->getData('area');
    }
}
