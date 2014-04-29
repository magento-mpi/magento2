<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Acl\Resource;

class Provider implements ProviderInterface
{
    /**
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $_configReader;

    /**
     * @var TreeBuilder
     */
    protected $_resourceTreeBuilder;

    /**
     * @param \Magento\Framework\Config\ReaderInterface $configReader
     * @param TreeBuilder $resourceTreeBuilder
     */
    public function __construct(\Magento\Framework\Config\ReaderInterface $configReader, TreeBuilder $resourceTreeBuilder)
    {
        $this->_configReader = $configReader;
        $this->_resourceTreeBuilder = $resourceTreeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getAclResources()
    {
        // TODO: As soon as all acl.xml files are moved to global scope, default ('global') scope should be used
        $aclResourceConfig = $this->_configReader->read('adminhtml');
        if (!empty($aclResourceConfig['config']['acl']['resources'])) {
            return $this->_resourceTreeBuilder->build($aclResourceConfig['config']['acl']['resources']);
        }
        return array();
    }
}
