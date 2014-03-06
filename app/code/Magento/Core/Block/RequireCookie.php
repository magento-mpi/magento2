<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Frontend form key content block
 */
namespace Magento\Core\Block;

class RequireCookie extends \Magento\View\Element\Template
{
    /**
     * Retrieve script options encoded to json
     *
     * @return string
     */
    public function getScriptOptions()
    {
        $params = array(
            'noCookieUrl' => $this->getUrl('core/index/noCookies/'),
            'triggers' => $this->getTriggers()
        );
        return json_encode($params);
    }
}
