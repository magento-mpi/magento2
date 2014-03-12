<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer;

interface StateKeyInterface
{
    /**
     * @param $category
     * @return string
     */
    public function toString($category);
}
