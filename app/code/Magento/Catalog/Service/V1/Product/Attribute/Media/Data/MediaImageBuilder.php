<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media\Data;

/**
 * Builder for media_image
 *
 * @codeCoverageIgnore
 */
class MediaImageBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set attribute code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(MediaImage::CODE, $code);
    }

    /**
     * Set attribute frontend label
     *
     * @param string $label
     * @return $this
     */
    public function setFrontendLabel($label)
    {
        return $this->_set(MediaImage::LABEL, $label);
    }

    /**
     * Set attribute scope. Valid values are 'Global', 'Website' and 'Store View'
     *
     * @param string $scope
     * @return $this
     */
    public function setScope($scope)
    {
        return $this->_set(MediaImage::SCOPE, $scope);
    }

    /**
     * Set true for user attributes or false for system attributes
     *
     * @param bool $isUserDefined
     * @return $this
     */
    public function setIsUserDefined($isUserDefined)
    {
        return $this->_set(MediaImage::IS_USER_DEFINED, $isUserDefined);
    }
}
