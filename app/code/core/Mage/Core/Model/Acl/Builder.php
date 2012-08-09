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
     * Area configuration
     *
     * @var Varien_Simplexml_Element
     */
    protected $_config;

    /**
     * Application config object
     *
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(array $data = array())
    {
        if (!isset($data['areaConfig'])) {
            throw new InvalidArgumentException('Area Config must be passed to ACL builder');
        }
        $this->_areaConfig = $data['areaConfig'];
        if (!isset($data['objectFactory'])) {
            throw new InvalidArgumentException('Object Factory must be passed to ACL builder');
        }
        $this->_objectFactory = $data['objectFactory'];
    }

    /**
     * Build Access Control List
     *
     * @return Magento_Acl
     * @throws LogicException
     */
    public function getAcl()
    {
        try {
            $acl = $this->_objectFactory->getModelInstance('Magento_Acl');
            $this->_objectFactory->getModelInstance($this->_getLoaderClass('resource'))->populateAcl($acl);
            $this->_objectFactory->getModelInstance($this->_getLoaderClass('role'))->populateAcl($acl);
            $this->_objectFactory->getModelInstance($this->_getLoaderClass('rule'))->populateAcl($acl);
        } catch (Exception $e) {
            throw new LogicException('Could not create acl object: ' . $e->getMessage());
        }

        return $acl;
    }

    /**
     * Retrieve ACL loader class from config or NullLoader if not defined
     *
     * @param string $loaderType
     * @return string
     */
    protected function _getLoaderClass($loaderType)
    {
        $loaderClass = (string) (isset($this->_areaConfig['acl'][$loaderType . 'Loader'])
            ? $this->_areaConfig['acl'][$loaderType . 'Loader']
            : '');

        return $loaderClass ?: 'Magento_Acl_Loader_Default';
    }
}
