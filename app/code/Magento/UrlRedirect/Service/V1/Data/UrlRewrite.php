<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObject;

/**
 * Data abstract class for url storage
 */
class UrlRewrite extends AbstractObject
{
    /**#@+
     * Value object attribute names
     */
    const ENTITY_ID = 'entity_id';
    const ENTITY_TYPE = 'entity_type';
    const REQUEST_PATH = 'request_path';
    const TARGET_PATH = 'target_path';
    const STORE_ID = 'store_id';
    const REDIRECT_TYPE = 'redirect_type';
    const DESCRIPTION = 'description';
    /**#@-*/

    /**
     * Get data by key
     *
     * @param string $key
     * @return mixed|null
     */
    public function getByKey($key)
    {
        return $this->_get($key);
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * @return int
     */
    public function getEntityType()
    {
        return $this->_get(self::ENTITY_TYPE);
    }

    /**
     * @return string
     */
    public function getRequestPath()
    {
        return $this->_get(self::REQUEST_PATH);
    }

    /**
     * @return string
     */
    public function getTargetPath()
    {
        return $this->_get(self::TARGET_PATH);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * @return string
     */
    public function getRedirectType()
    {
        return $this->_get(self::REDIRECT_TYPE);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }
}
