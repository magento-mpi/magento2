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
     * @var \Magento\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\App\Dir $dir
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Page $page
     * @param \Magento\Core\Model\Page\Asset\MergeService $assetMergeService
     * @param \Magento\Core\Model\Page\Asset\MinifyService $assetMinifyService
     * @param \Magento\Data\Form\FormKey $formKey
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\App\Dir $dir,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\Page $page,
        \Magento\Core\Model\Page\Asset\MergeService $assetMergeService,
        \Magento\Core\Model\Page\Asset\MinifyService $assetMinifyService,
        \Magento\Data\Form\FormKey $formKey,
        array $data = array()
    ) {
        parent::__construct(
            $locale, $dir, $storeManager, $fileStorageDatabase, $coreData, $context, $objectManager, $page,
            $assetMergeService, $assetMinifyService, $data
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
}
