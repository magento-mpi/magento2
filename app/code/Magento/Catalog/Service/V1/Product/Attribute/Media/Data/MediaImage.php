<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media\Data;

/**
 * Contains media_image attribute info
 */
class MediaImage extends \Magento\Framework\Service\Data\AbstractObject
{
    const CODE = 'code';

    const SCOPE = 'scope';

    const LABEL = 'frontend_label';

    const IS_USER_DEFINED = 'is_user_defined';

    /**
     * attribute code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Return values are 'Global', 'Website' or 'Store View'
     *
     * @return string
     */
    public function getScope()
    {
        return $this->_get(self::SCOPE);
    }

    /**
     * @return string
     */
    public function getFrontendLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * @return bool
     */
    public function getIsUserDefined()
    {
        return (bool)$this->_get(self::IS_USER_DEFINED);
    }
}