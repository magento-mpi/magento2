<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Service\V1;

use Magento\UrlRewrite\Model\StorageInterface;
use Magento\UrlRewrite\Model\Storage\DuplicateEntryException;

/**
 * Url Manager
 */
class UrlManager implements UrlFinderInterface, UrlPersistInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $urls)
    {
        try {
            $this->storage->replace($urls);
        } catch (DuplicateEntryException $e) {
            throw new DuplicateEntryException(__('URL key for specified store already exists.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByData(array $data)
    {
        $this->storage->deleteByData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByData(array $data)
    {
        return $this->storage->findOneByData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByData(array $data)
    {
        return $this->storage->findAllByData($data);
    }
}
