<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Block_Cache_Notifications extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Core_Model_Authorization
     */
    protected $_authorization;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Authorization $authorization
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Authorization $authorization,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_authorization = $authorization;
    }

    /**
     * Get array of cache types which require data refresh
     *
     * @return array
     */
    public function getCacheTypesForRefresh()
    {
        $invalidatedTypes = $this->_cache->getInvalidatedTypes();
        $res = array();
        foreach ($invalidatedTypes as $type) {
            $res[] = $type->getCacheType();
        }
        return $res;
    }

    /**
     * Get index management url
     *
     * @return string
     */
    public function getManageUrl()
    {
        return $this->getUrl('adminhtml/cache');
    }

    /**
     * ACL validation before html generation
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_authorization->isAllowed('Mage_Adminhtml::cache')) {
            return parent::_toHtml();
        }
        return '';
    }
}
