<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Block;

/**
 * Class Form
 */
class Javascript extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve script options encoded to json
     *
     * @return string
     */
    public function getScriptOptions()
    {
        $params = [
            'url' => $this->getUrl('page_cache/block/render/', ['_current' => true]),
            'handles' => $this->_layout->getUpdate()->getHandles(),
            'versionCookieName' => \Magento\Framework\App\PageCache\Version::COOKIE_NAME,
        ];
        return json_encode($params);
    }
}
