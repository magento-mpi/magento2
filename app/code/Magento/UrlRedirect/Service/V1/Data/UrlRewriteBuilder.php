<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;

/**
 * Data builder class for url rewrite
 */
class UrlRewriteBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->_set(UrlRewrite::ENTITY_ID, $entityId);
    }

    /**
     * @param int $entityType
     *
     * @return $this
     */
    public function setEntityType($entityType)
    {
        return $this->_set(UrlRewrite::ENTITY_TYPE, $entityType);
    }

    /**
     * @param string $requestPath
     *
     * @return $this
     */
    public function setRequestPath($requestPath)
    {
        return $this->_set(UrlRewrite::REQUEST_PATH, $requestPath);
    }

    /**
     * @param string $targetPath
     *
     * @return $this
     */
    public function setTargetPath($targetPath)
    {
        return $this->_set(UrlRewrite::TARGET_PATH, $targetPath);
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->_set(UrlRewrite::STORE_ID, $storeId);
    }

    /**
     * @param int $redirectCode
     *
     * @return $this
     */
    public function setRedirectCode($redirectCode)
    {
        return $this->_set(UrlRewrite::REDIRECT_TYPE, $redirectCode);
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->_set(UrlRewrite::DESCRIPTION, $description);
    }
}
