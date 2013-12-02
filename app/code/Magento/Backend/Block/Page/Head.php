<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml header block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Page;

class Head extends \Magento\Theme\Block\Html\Head
{
    /**
     * @var string
     */
    protected $_template = 'page/head.phtml';

    /**
     * @var \Magento\App\Action\Title
     */
    protected $_titles;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\View\Asset\GroupedCollection $assets
     * @param \Magento\View\Asset\MergeService $assetMergeService
     * @param \Magento\View\Asset\MinifyService $assetMinifyService
     * @param \Magento\App\Action\Title $titles
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase,
        \Magento\ObjectManager $objectManager,
        \Magento\View\Asset\GroupedCollection $assets,
        \Magento\View\Asset\MergeService $assetMergeService,
        \Magento\View\Asset\MinifyService $assetMinifyService,
        \Magento\App\Action\Title $titles,
        array $data = array()
    ) {
        $this->_titles = $titles;
        parent::__construct(
            $context,
            $fileStorageDatabase,
            $objectManager,
            $assets,
            $assetMergeService,
            $assetMinifyService,
            $data
        );
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->_session->getFormKey();
    }

    /**
     * @return array|string
     */
    public function getTitle()
    {
        /** Get default title */
        $title = parent::getTitle();

        /** Add default title */
        $this->_titles->add($title, true);

        /** Set title list */
        $this->setTitle(array_reverse($this->_titles->get()));

        /** Render titles */
        return parent::getTitle();
    }
}
