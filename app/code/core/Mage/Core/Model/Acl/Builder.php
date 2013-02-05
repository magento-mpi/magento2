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
     * @var Magento_Acl
     */
    protected $_acl;

    /**
     * Acl loader list
     *
     * @var Mage_Core_Model_Acl_LoaderPool
     */
    protected $_loaderPool;

    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(Magento_Acl $acl, Mage_Core_Model_Acl_LoaderPool $loaderPool)
    {
        $this->_acl = $acl;
        $this->_loaderPool = $loaderPool;
    }

    /**
     * Build Access Control List
     *
     * @return Magento_Acl
     * @throws LogicException
     */
    public function getAcl()
    {
        if (!count($this->_acl->getResources())) {
            try {
                /** @var $loader Magento_Acl_Loader */
                foreach ($this->_loaderPool as $loader) {
                    $loader->populateAcl($this->_acl);
                }
            } catch (Exception $e) {
                throw new LogicException('Could not create acl object: ' . $e->getMessage());
            }
        }
        return $this->_acl;
    }
}
