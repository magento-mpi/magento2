<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin rule resource model
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Model_Resource_Rules extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Root ACL resource
     *
     * @var Magento_Core_Model_Acl_RootResource
     */
    protected $_rootResource;

    /**
     * Acl object cache
     *
     * @var Magento_Acl_CacheInterface
     */
    protected $_aclCache;

    /**
     * @var Magento_Acl_Builder
     */
    protected $_aclBuilder;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Acl_Builder $aclBuilder
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Acl_RootResource $rootResource
     * @param Magento_Acl_CacheInterface $aclCache
     */
    public function __construct(
        Magento_Acl_Builder $aclBuilder,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Acl_RootResource $rootResource,
        Magento_Acl_CacheInterface $aclCache
    ) {
        $this->_aclBuilder = $aclBuilder;
        parent::__construct($resource);
        $this->_rootResource = $rootResource;
        $this->_aclCache = $aclCache;
        $this->_logger = $logger;
    }

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('admin_rule', 'rule_id');
    }

    /**
     * Save ACL resources
     *
     * @param Magento_User_Model_Rules $rule
     * @throws Magento_Core_Exception
     */
    public function saveRel(Magento_User_Model_Rules $rule)
    {
        try {
            $adapter = $this->_getWriteAdapter();
            $adapter->beginTransaction();
            $roleId = $rule->getRoleId();

            $condition = array(
                'role_id = ?' => (int) $roleId,
            );

            $adapter->delete($this->getMainTable(), $condition);

            $postedResources = $rule->getResources();
            if ($postedResources) {
                $row = array(
                    'role_type'   => 'G',
                    'resource_id' => $this->_rootResource->getId(),
                    'privileges'  => '', // not used yet
                    'role_id'     => $roleId,
                    'permission'  => 'allow'
                );

                // If all was selected save it only and nothing else.
                if ($postedResources === array($this->_rootResource->getId())) {
                    $insertData = $this->_prepareDataForTable(new Magento_Object($row), $this->getMainTable());

                    $adapter->insert($this->getMainTable(), $insertData);
                } else {
                    $acl = $this->_aclBuilder->getAcl();
                    /** @var $resource Magento_Acl_Resource */
                    foreach ($acl->getResources() as $resourceId) {
                        $row['permission'] = in_array($resourceId, $postedResources) ? 'allow' : 'deny';
                        $row['resource_id'] = $resourceId;

                        $insertData = $this->_prepareDataForTable(new Magento_Object($row), $this->getMainTable());
                        $adapter->insert($this->getMainTable(), $insertData);
                    }
                }
            }

            $adapter->commit();
            $this->_aclCache->clean();
        } catch (Magento_Core_Exception $e) {
            $adapter->rollBack();
            throw $e;
        } catch (Exception $e){
            $adapter->rollBack();
            $this->_logger->logException($e);
        }
    }
}
