<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Option;

use Magento\Framework\Service\Data\AbstractObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class MetadataBuilder extends AbstractObjectBuilder
{
    /**
     * Set option id
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(Metadata::ID, $value);
    }

    /**
     * Set option title
     *
     * @param string $value
     * @return $this
     */
    public function setTitle($value)
    {
        return $this->_set(Metadata::TITLE, $value);
    }

    /**
     * Set is required option
     *
     * @param bool $value
     * @return $this
     */
    public function setRequired($value)
    {
        return $this->_set(Metadata::REQUIRED, $value);
    }

    /**
     * Set input type
     *
     * @param string $value
     * @return $this
     */
    public function setType($value)
    {
        return $this->_set(Metadata::TYPE, $value);
    }

    /**
     * Set option position
     *
     * @param int $value
     * @return $this
     */
    public function setPosition($value)
    {
        return $this->_set(Metadata::POSITION, $value);
    }
}
