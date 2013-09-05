<?php
/**
 * Grid row url generator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Widget_Grid_Row_UrlGeneratorId
    implements Magento_Backend_Model_Widget_Grid_Row_GeneratorInterface
{
    /**
     * Create url for passed item using passed url model
     * @param \Magento\Object $item
     * @return string
     */
    public function getUrl($item)
    {
        return $item->getId();
    }
}
