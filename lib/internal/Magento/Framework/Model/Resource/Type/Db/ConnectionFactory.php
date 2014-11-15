<?php
/**
 * Connection adapter factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Model\Resource\Type\Db;

use Magento\Framework\ObjectManager;

class ConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $connectionConfig)
    {
        if (!$connectionConfig || !isset($connectionConfig['active']) || !$connectionConfig['active']) {
            return null;
        }

        $adapterInstance = $this->objectManager->create(
            'Magento\Framework\App\Resource\ConnectionAdapterInterface',
            ['config' => $connectionConfig]
        );

        return $adapterInstance->getConnection($this->objectManager->get('Magento\Framework\DB\LoggerInterface'));
    }
}
