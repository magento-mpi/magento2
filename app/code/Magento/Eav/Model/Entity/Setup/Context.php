<?php
/**
 * Eav setup context object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Setup;

class Context extends \Magento\Module\Setup\Context
{
    /**
     * @var PropertyMapperInterface
     */
    protected $attributeMapper;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Module\Dir\Reader $modulesReader
     * @param \Magento\Module\ModuleListInterface $moduleList
     * @param \Magento\Module\ResourceInterface $resourceResource
     * @param \Magento\Module\Setup\MigrationFactory $migrationFactory
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param PropertyMapperInterface $attributeMapper
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Resource $resource,
        \Magento\Module\Dir\Reader $modulesReader,
        \Magento\Module\ModuleListInterface $moduleList,
        \Magento\Module\ResourceInterface $resourceResource,
        \Magento\Module\Setup\MigrationFactory $migrationFactory,
        \Magento\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\App\Filesystem $filesystem,
        PropertyMapperInterface $attributeMapper
    ) {
        $this->attributeMapper = $attributeMapper;
        parent::__construct(
            $logger,
            $eventManager,
            $resource,
            $modulesReader,
            $moduleList,
            $resourceResource,
            $migrationFactory,
            $encryptor,
            $filesystem
        );
    }

    /**
     * @return PropertyMapperInterface
     */
    public function getAttributeMapper()
    {
        return $this->attributeMapper;
    }
}
