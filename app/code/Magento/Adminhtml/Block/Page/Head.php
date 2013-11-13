<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml header block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Page;

class Head extends \Magento\Page\Block\Html\Head
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
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Page $page
     * @param \Magento\Core\Model\Page\Asset\MergeService $assetMergeService
     * @param \Magento\Core\Model\Page\Asset\MinifyService $assetMinifyService
     * @param \Magento\App\Action\Title $titles
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase,
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\Page $page,
        \Magento\Core\Model\Page\Asset\MergeService $assetMergeService,
        \Magento\Core\Model\Page\Asset\MinifyService $assetMinifyService,
        \Magento\App\Action\Title $titles,
        array $data = array()
    ) {
        $this->_titles = $titles;
        parent::__construct(
            $context,
            $locale,
            $fileStorageDatabase,
            $coreData,
            $objectManager,
            $page,
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
