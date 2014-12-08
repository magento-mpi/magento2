<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Frontend form key content block
 */
namespace Magento\Core\Block;

class RequireCookie extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve script options encoded to json
     *
     * @return string
     */
    public function getScriptOptions()
    {
        $params = ['noCookieUrl' => $this->getUrl('core/index/noCookies/'), 'triggers' => $this->getTriggers()];
        return json_encode($params);
    }
}
