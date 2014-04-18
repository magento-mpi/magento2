<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Resource;

/**
 * Admin rule resource model
 */
class Rules extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Root ACL resource
     *
     * @var \Magento\Acl\RootResource
     */
    protected $_rootResource;

    /**
     * Acl object cache
     *
     * @var \Magento\Acl\CacheInterface
     */
    protected $_aclCache;

    /**
     * @var \Magento\Acl\Builder
     */
    protected $_aclBuilder;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Acl\Builder $aclBuilder
     * @param \Magento\Logger $logger
     * @param \Magento\Acl\RootResource $rootResource
     * @param \Magento\Acl\CacheInterface $aclCache
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Acl\Builder $aclBuilder,
        \Magento\Logger $logger,
        \Magento\Acl\RootResource $rootResource,
        \Magento\Acl\CacheInterface $aclCache
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
     * @return void
     */
    protected function _construct()
    {
        $this->_init('admin_rule', 'rule_id');
    }

    /**
     * Save ACL resources
     *
     * @param \Magento\User\Model\Rules $rule
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function saveRel(\Magento\User\Model\Rules $rule)
    {
        try {
            $adapter = $this->_getWriteAdapter();
            $adapter->beginTransaction();
            $roleId = $rule->getRoleId();

            $condition = array('role_id = ?' => (int)$roleId);

            $adapter->delete($this->getMainTable(), $condition);

            $postedResources = $rule->getResources();
            if ($postedResources) {
                $row = array(
                    'resource_id' => $this->_rootResource->getId(),
                    'privileges' => '', // not used yet
                    'role_id' => $roleId,
                    'permission' => 'allow'
                );

                // If all was selected save it only and nothing else.
                if ($postedResources === array($this->_rootResource->getId())) {
                    $insertData = $this->_prepareDataForTable(new \Magento\Object($row), $this->getMainTable());

                    $adapter->insert($this->getMainTable(), $insertData);
                } else {
                    $acl = $this->_aclBuilder->getAcl();
                    /** @var $resource \Magento\Acl\Resource */
                    foreach ($acl->getResources() as $resourceId) {
                        $row['permission'] = in_array($resourceId, $postedResources) ? 'allow' : 'deny';
                        $row['resource_id'] = $resourceId;

                        $insertData = $this->_prepareDataForTable(new \Magento\Object($row), $this->getMainTable());
                        $adapter->insert($this->getMainTable(), $insertData);
                    }
                }
            }

            $adapter->commit();
            $this->_aclCache->clean();
        } catch (\Magento\Framework\Model\Exception $e) {
            $adapter->rollBack();
            throw $e;
        } catch (\Exception $e) {
            $adapter->rollBack();
            $this->_logger->logException($e);
        }
    }
}
