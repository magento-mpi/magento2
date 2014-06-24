<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\Data;

use \Magento\Framework\Service\Data\AbstractObject;

class FileContent extends AbstractObject
{
    const DATA = 'data';
    const NAME = 'name';

    /**
     * Retrieve data (base64 encoded content)
     *
     * @return string
     */
    public function getData()
    {
        return $this->_get(self::DATA);
    }

    /**
     * Retrieve file name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }
}
