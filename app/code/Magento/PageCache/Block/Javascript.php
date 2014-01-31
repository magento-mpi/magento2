<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Block;

/**
 * Class Form
 * @package Magento\PageCache\Block
 */
class Javascript extends \Magento\View\Element\Template
{
    /**
     * Retrieve script options encoded to json
     *
     * @return string
     */
    public function getScriptOptions()
    {
        $params = array(
            'url' => $this->getUrl('page_cache/block/render/'),
            'handles' => $this->getLayout()->getUpdate()->getHandles(),
            'versionCookieName' => \Magento\PageCache\Model\Version::COOKIE_NAME
        );
        return json_encode($params);
    }
}
