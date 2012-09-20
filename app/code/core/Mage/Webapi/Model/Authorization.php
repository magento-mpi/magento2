<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Authorization model
 *
 * Checks if ACL Resource for Webapi Role is allowed, according to Webapi Access Control List
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Authorization
{

    const RESOURCE_SEPARATOR = '/';

    /**
     * ACL policy
     *
     * @var Magento_Authorization_Policy
     */
    protected $_aclPolicy;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_aclPolicy = isset($data['policy']) ? $data['policy'] : $this->_getAclPolicy();
    }

    /**
     * Get ACL policy object
     *
     * @return Magento_Authorization_Policy
     * @throws InvalidArgumentException
     */
    protected function _getAclPolicy()
    {
        $areaConfig = Mage::getConfig()->getAreaConfig();
        $policyClassName = isset($areaConfig['acl']['policy']) ?
            $areaConfig['acl']['policy'] :
            'Magento_Authorization_Policy_Default';

        /** @var $aclBuilder Mage_Core_Model_Acl_Builder */
        $aclBuilder = Mage::getSingleton('Mage_Core_Model_Acl_Builder', array(
            'areaConfig' => $areaConfig,
            'objectFactory' => Mage::getConfig(),
        ));

        /** @var $policyObject Magento_Authorization_Policy **/
        $policyObject = new $policyClassName($aclBuilder->getAcl());
        if (!($policyObject instanceof Magento_Authorization_Policy)) {
            throw new InvalidArgumentException($policyClassName . ' is not instance of Magento_Authorization_Policy');
        }

        return $policyObject;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @param string $roleId
     * @param string $resource
     * @param string $operation
     * @return bool
     */
    public function isAllowed($roleId, $resource, $operation)
    {
        $aclResource = $resource . self::RESOURCE_SEPARATOR . $operation;
        return $this->_aclPolicy->isAllowed($roleId, $aclResource, null);
    }
}
