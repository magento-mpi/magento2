<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Translate;

/**
 * Inline Translations PHP part
 */
class Inline extends \Magento\Translate\Inline
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

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string &$body
     * @param bool $isJson
     * @return $this
     */
    public function processResponseBody(&$body, $isJson = false)
    {
        if (!$this->isAllowed()) {
            $this->_stripInlineTranslations($body);
            return $this;
        }
        return parent::processResponseBody($body, $isJson);
    }
}
