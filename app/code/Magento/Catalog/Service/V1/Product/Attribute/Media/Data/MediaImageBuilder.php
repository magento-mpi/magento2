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
 */
class MediaImageBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    public function setCode($code)
    {
        return $this->_set(MediaImage::CODE, $code);
    }

    public function setFrontendLabel($label)
    {
        return $this->_set(MediaImage::LABEL, $label);
    }

    public function setScope($scope)
    {
        return $this->_set(MediaImage::SCOPE, $scope);
    }

    public function setIsUserDefined($isUserDefined)
    {
        return $this->_set(MediaImage::IS_USER_DEFINED, $isUserDefined);
    }
}