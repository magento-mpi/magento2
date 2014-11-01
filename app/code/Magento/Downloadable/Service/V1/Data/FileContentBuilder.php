<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\Data;

use \Magento\Framework\Api\AbstractExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class FileContentBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * Set data (base64 encoded content)
     *
     * @param string $data
     * @return $this
     */
    public function setData($data)
    {
        return $this->_set(FileContent::DATA, $data);
    }

    /**
     * Set file name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->_set(FileContent::NAME, $name);
    }
}
