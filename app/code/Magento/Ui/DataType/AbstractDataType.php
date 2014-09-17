<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\DataType;

use Magento\Ui\AbstractView;
use Magento\Framework\Object as DataObject;

/**
 * Class AbstractDataType
 */
abstract class AbstractDataType extends AbstractView implements DataTypeInterface
{
    /**
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getDataObjectValue()
    {
        return $this->getData('data_object')[$this->getData('name')];
    }
}
