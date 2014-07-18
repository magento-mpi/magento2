<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1\Storage\Data;

use \Magento\Framework\Service\Data\AbstractObjectBuilder;

/**
 * Data builder class for url storage
 */
abstract class AbstractBuilder extends AbstractObjectBuilder
{
    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->_set(AbstractData::ENTITY_ID, $entityId);
    }

    /**
     * @param int $entityType
     *
     * @return $this
     */
    public function setEntityType($entityType)
    {
        return $this->_set(AbstractData::ENTITY_TYPE, $entityType);
    }

    /**
     * @param string $requestPath
     *
     * @return $this
     */
    public function setRequestPath($requestPath)
    {
        return $this->_set(AbstractData::REQUEST_PATH, $requestPath);
    }

    /**
     * @param string $targetPath
     *
     * @return $this
     */
    public function setTargetPath($targetPath)
    {
        return $this->_set(AbstractData::TARGET_PATH, $targetPath);
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->_set(AbstractData::STORE_ID, $storeId);
    }

    /**
     * @param int $redirectCode
     *
     * @return $this
     */
    public function setRedirectCode($redirectCode)
    {
        return $this->_set(AbstractData::REDIRECT_TYPE, $redirectCode);
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->_set(AbstractData::DESCRIPTION, $description);
    }
}
