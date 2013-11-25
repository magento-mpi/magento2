<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Inline Translations PHP part
 */
namespace Magento\Backend\Model\Translate;

class Inline extends \Magento\Core\Model\Translate\Inline
{
    /**
     * Return URL for ajax requests
     *
     * @return string
     */
    protected function _getAjaxUrl()
    {
        return $this->_url->getUrl(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE . '/ajax/translate');
    }
}
