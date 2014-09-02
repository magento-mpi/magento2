<?php
/**
 * Search Engine
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

class SearchEngine implements SearchEngineInterface
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @param AdapterFactory $adapterFactory
     */
    public function __construct(
        AdapterFactory $adapterFactory
    ) {
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
