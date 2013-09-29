<?php
/**
 * Grid row url generator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Widget\Grid\Row;

class UrlGeneratorId
    implements \Magento\Backend\Model\Widget\Grid\Row\GeneratorInterface
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
