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
 * Css page block
 */
class Css extends \Magento\Core\Block\AbstractBlock
    implements \Magento\Page\Block\Html\Head\AssetBlock
{
    /**
     * Contructor
     *
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Page\Asset\ViewFileFactory $viewFileFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Page\Asset\ViewFileFactory $viewFileFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->setAsset(
            $viewFileFactory->create(array(
                'file' => (string)$this->getFile(),
                'contentType' => \Magento\Core\Model\View\Publisher::CONTENT_TYPE_CSS
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
