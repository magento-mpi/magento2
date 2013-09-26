<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable sample model
 *
 * @method \Magento\Downloadable\Model\Resource\Sample _getResource()
 * @method \Magento\Downloadable\Model\Resource\Sample getResource()
 * @method int getProductId()
 * @method \Magento\Downloadable\Model\Sample setProductId(int $value)
 * @method string getSampleUrl()
 * @method \Magento\Downloadable\Model\Sample setSampleUrl(string $value)
 * @method string getSampleFile()
 * @method \Magento\Downloadable\Model\Sample setSampleFile(string $value)
 * @method string getSampleType()
 * @method \Magento\Downloadable\Model\Sample setSampleType(string $value)
 * @method int getSortOrder()
 * @method \Magento\Downloadable\Model\Sample setSortOrder(int $value)
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Model;

class Sample extends \Magento\Core\Model\AbstractModel
{
    const XML_PATH_SAMPLES_TITLE = 'catalog/downloadable/samples_title';

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dirModel;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Dir $dirModel
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Dir $dirModel,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_dirModel = $dirModel;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Downloadable\Model\Resource\Sample');
        parent::_construct();
    }

    /**
     * Return sample files path
     *
     * @return string
     */
    public function getSampleDir()
    {
        return $this->_dirModel->getDir();
    }

    /**
     * After save process
     *
     * @return \Magento\Downloadable\Model\Sample
     */
    protected function _afterSave()
    {
        $this->getResource()
            ->saveItemTitle($this);
        return parent::_afterSave();
    }

    /**
     * Retrieve sample URL
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->getSampleUrl()) {
            return $this->getSampleUrl();
        } else {
            return $this->getSampleFile();
        }
    }

    /**
     * Retrieve base tmp path
     *
     * @return string
     */
    public function getBaseTmpPath()
    {
        return $this->_dirModel->getDir(\Magento\Core\Model\Dir::MEDIA)
            . DS . 'downloadable' . DS . 'tmp' . DS . 'samples';
    }

    /**
     * Retrieve sample files path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->_dirModel->getDir(\Magento\Core\Model\Dir::MEDIA)
            . DS . 'downloadable' . DS . 'files' . DS . 'samples';
    }

    /**
     * Retrieve links searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        return $this->_getResource()
            ->getSearchableData($productId, $storeId);
    }
}
