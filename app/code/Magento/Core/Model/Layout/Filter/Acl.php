<?php
/**
 * ACL block filter
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Layout_Filter_Acl
{
    /**
     * Authorization
     *
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(\Magento\AuthorizationInterface $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Delete nodes that have "acl" attribute but value is "not allowed"
     * In any case, the "acl" attribute will be unset
     *
     * @param \Magento\Simplexml\Element $xml
     */
    public function filterAclNodes(\Magento\Simplexml\Element $xml)
    {
        $limitations = $xml->xpath('//*[@acl]') ?: array();
        foreach ($limitations as $node) {
            if (!$this->_authorization->isAllowed($node['acl'])) {
                $node->unsetSelf();
            } else {
                unset($node['acl']);
            }
        }
    }
}
