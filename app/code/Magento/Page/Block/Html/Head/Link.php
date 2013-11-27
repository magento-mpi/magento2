<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block\Html\Head;

/**
 * Link page block
 */
class Link extends \Magento\View\Element\Template
    implements \Magento\Page\Block\Html\Head\AssetBlock
{
    const VIRTUAL_CONTENT_TYPE = 'link';

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Page\Asset\RemoteFactory $remoteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Page\Asset\RemoteFactory $remoteFactory,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
        $this->setAsset(
            $remoteFactory->create(array(
                'url' => (string)$this->getData('url'),
                'contentType' => self::VIRTUAL_CONTENT_TYPE,
            ))
        );
    }

    /**
     * Get block asset
     *
     * @return \Magento\Core\Model\Page\Asset\AssetInterface
     */
    public function getAsset()
    {
        return $this->_getData('asset');
    }
}
