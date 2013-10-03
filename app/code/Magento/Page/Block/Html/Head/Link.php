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
class Link extends \Magento\Core\Block\Template
    implements \Magento\Page\Block\Html\Head\AssetBlock
{
    const VIRTUAL_CONTENT_TYPE = 'link';

    /**
     * Contructor
     *
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Page\Asset\RemoteFactory $remoteFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Page\Asset\RemoteFactory $remoteFactory,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
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
