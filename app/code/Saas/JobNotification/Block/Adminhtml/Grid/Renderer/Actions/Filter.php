<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_JobNotification_Block_Adminhtml_Grid_Renderer_Actions_Filter
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(Magento_AuthorizationInterface $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Check whether action is allowed
     *
     * @param array $actionConfig
     * @return bool
     */
    protected function _isAllowed($actionConfig)
    {
        return isset($actionConfig['acl']) ? $this->_authorization->isAllowed($actionConfig['acl']) : false;
    }

    /**
     * Check whether action item should be ignored
     *
     * @param array $actionConfig
     * @param Varien_Object $row
     * @return bool
     */
    protected function _isIgnored(array $actionConfig, Varien_Object $row)
    {
        $property = isset($actionConfig['ignore_condition']) ? $actionConfig['ignore_condition'] : false;

        if (false === $property) {
            return false;
        }

        return $row->hasData($property) ? (bool)($row->getData($property) * 1) : true;
    }

    /**
     * Check whether action item should be rendered
     *
     * @param array $config
     * @param Varien_Object $object
     * @return bool
     */
    public function isAllowed(array $config, Varien_Object $object)
    {
        return $this->_isAllowed($config) && false == $this->_isIgnored($config, $object);
    }
}
