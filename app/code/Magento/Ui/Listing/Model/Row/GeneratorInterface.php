<?php
/**
 * Row Generator Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Model\Row;

interface GeneratorInterface
{
    /**
     * @param \Magento\Framework\Object $item
     * @return string
     */
    public function getUrl($item);
}
