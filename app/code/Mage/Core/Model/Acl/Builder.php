<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Access Control List Builder. Retrieves required role/rule/resource loaders from configuration and uses them
 * to populate provided ACL object. If loaders are not defined - default loader is used that does not do anything
 * to ACL
 */
class Mage_Core_Model_Acl_Builder
{
    /**
     * Acl object
     *
     * @var Magento_Acl[]
     */
    protected $_aclPool;

    /**
     * Acl loader list
     *
     * @var Mage_Core_Model_Acl_LoaderPool
     */
    protected $_loaderPool;

    /**
     * @param Magento_AclFactory $aclFactory
     * @param Mage_Core_Model_Acl_LoaderPool $loaderPool
     */
    public function __construct(Magento_AclFactory $aclFactory, Mage_Core_Model_Acl_LoaderPool $loaderPool)
    {
        $this->_aclFactory = $aclFactory;
        $this->_loaderPool = $loaderPool;
    }

    /**
     * Build Access Control List
     *
     * @param string $areaCode
     * @return Magento_Acl
     * @throws LogicException
     */
    public function getAcl($areaCode)
    {
        if (!isset($this->_aclPool[$areaCode])) {
            try {
                $this->_aclPool[$areaCode] = $this->_aclFactory->create();
                /** @var $loader Magento_Acl_Loader */
                foreach ($this->_loaderPool->getLoadersByArea($areaCode) as $loader) {
                    $loader->populateAcl($this->_aclPool[$areaCode]);
                }
            } catch (Exception $e) {
                throw new LogicException('Could not create acl object: ' . $e->getMessage());
            }
        }
        return $this->_aclPool[$areaCode];
    }
}
