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
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\View\Asset\GroupedCollection $assets
     * @param \Magento\Framework\View\Asset\MergeService $assetMergeService
     * @param \Magento\Framework\View\Asset\MinifyService $assetMinifyService
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Translation\Block\Js $jsTranslation
     * @param \Magento\Framework\App\Action\Title $titles
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase,
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\View\Asset\GroupedCollection $assets,
        \Magento\Framework\View\Asset\MergeService $assetMergeService,
        \Magento\Framework\View\Asset\MinifyService $assetMinifyService,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Translation\Block\Js $jsTranslation,
        \Magento\Framework\App\Action\Title $titles,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = array()
    ) {
        $this->_titles = $titles;
        $this->formKey = $formKey;
        parent::__construct(
            $context,
            $fileStorageDatabase,
            $objectManager,
            $assets,
            $assetMergeService,
            $assetMinifyService,
            $localeResolver,
            $jsTranslation,
            $data
        );
        $this->formKey = $formKey;
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
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
