<?php
/**
 * Downloadable Link Builder
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\DownloadableLink\Data;

class DownloadableResourceInfoBuilder extends AbstractObjectBuilder
{
    /**
     * Set file path
     *
     * @param string|null $value
     * @return mixed
     */
    public function setFile($value)
    {
        return $this->_set(DownloadableResourceInfo::FILE, $value);
    }

    /**
     * Set URL
     *
     * @param sting|null $value
     * @return mixed
     */
    public function setUrl($value)
    {
        return $this->_set(DownloadableResourceInfo::URL, $value);
    }

    /**
     * Set value type
     *
     * @param string $value
     * @throws \Magento\Framework\Exception\InputException
     * @return mixed
     */
    public function setType($value)
    {
        $allowedValues = ['url', 'file'];
        if (!in_array($value, $allowedValues)) {
            $values = '\'' . implode('\' and \'', $allowedValues) . '\'';
            throw new \Magento\Framework\Exception\InputException('Allowed type values are '. $values );
        }
        return $this->_set(DownloadableResourceInfo::TYPE, $value);
    }


}