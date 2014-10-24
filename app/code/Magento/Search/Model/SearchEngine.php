<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\SearchEngineInterface;

/**
 * Search Engine
 */
class SearchEngine implements SearchEngineInterface
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @param AdapterFactory $adapterFactory
     */
    public function __construct(AdapterFactory $adapterFactory)
    {
        $this->adapter = $adapterFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function search(RequestInterface $request)
    {
        return $this->adapter->query($request);
    }
}
