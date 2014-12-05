<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\Stub;

use Magento\Framework\Data\AbstractDataObject;

class DataObject extends AbstractDataObject
{
    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get($key)
    {
        return parent::get($key);
    }
}
 