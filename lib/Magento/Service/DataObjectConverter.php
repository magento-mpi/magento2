<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service;

use Magento\Service\Entity\AbstractObject;
use Magento\Convert\ConvertArray;

class DataObjectConverter
{
    /**
     * Convert nested array into flat array.
     *
     * @param AbstractObject $dataObject
     * @return array
     */
    public static function toFlatArray(AbstractObject $dataObject)
    {
        $data = $dataObject->__toArray();
        return ConvertArray::toFlatArray($data);

    }
} 