<?php
/**
 * Grid row url generator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Model\Row;

class UrlGeneratorId implements \Magento\Ui\Listing\Model\Row\GeneratorInterface
{
    /**
     * Create url for passed item using passed url model
     *
     * @param \Magento\Framework\Object $item
     * @return string
     */
    public function getUrl($item)
    {
        return $item->getId();
    }
}
