<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Source\Hierarchy\Menu;

/**
 * CMS Hierarchy Navigation Menu source model for Display list mode
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Listmode implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Retrieve options array
     *
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            ''          => __('Default'),
            '1'         => __('Numbers (1, 2, 3, ...)'),
            'a'         => __('Lower Alpha (a, b, c, ...)'),
            'A'         => __('Upper Alpha (A, B, C, ...)'),
            'i'         => __('Lower Roman (i, ii, iii, ...)'),
            'I'         => __('Upper Roman (I, II, III, ...)'),
            'circle'    => __('Circle'),
            'disc'      => __('Disc'),
            'square'    => __('Square'),
        );
    }
}
