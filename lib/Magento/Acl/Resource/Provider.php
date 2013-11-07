<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Acl\Resource;

class Provider implements \Magento\Acl\Resource\ProviderInterface
{
    /**
     * @var \Magento\Config\ReaderInterface
     */
    protected $_configReader;

    /**
     * @var \Magento\Acl\Resource\TreeBuilder
     */
    protected $_resourceTreeBuilder;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Config\ReaderInterface $configReader
     * @param \Magento\Acl\Resource\TreeBuilder $resourceTreeBuilder
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\Config\ReaderInterface $configReader,
        \Magento\Acl\Resource\TreeBuilder $resourceTreeBuilder,
        \Magento\App\State $appState
    ) {
        $this->_configReader = $configReader;
        $this->_resourceTreeBuilder = $resourceTreeBuilder;
        $this->_appState = $appState;
    }

    /**
     * {@inheritdoc}
     */
    public function getAclResources()
    {
        $aclResourceConfig = $this->_configReader->read($this->_appState->getAreaCode());
        if (!empty($aclResourceConfig['config']['acl']['resources'])) {
            return $this->_resourceTreeBuilder->build($aclResourceConfig['config']['acl']['resources']);
        }
        return array();
    }

}
