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
 * @method Magento_Downloadable_Model_Resource_Sample _getResource()
 * @method Magento_Downloadable_Model_Resource_Sample getResource()
 * @method int getProductId()
 * @method Magento_Downloadable_Model_Sample setProductId(int $value)
 * @method string getSampleUrl()
 * @method Magento_Downloadable_Model_Sample setSampleUrl(string $value)
 * @method string getSampleFile()
 * @method Magento_Downloadable_Model_Sample setSampleFile(string $value)
 * @method string getSampleType()
 * @method Magento_Downloadable_Model_Sample setSampleType(string $value)
 * @method int getSortOrder()
 * @method Magento_Downloadable_Model_Sample setSortOrder(int $value)
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_Sample extends Magento_Core_Model_Abstract
{
    const XML_PATH_SAMPLES_TITLE = 'catalog/downloadable/samples_title';

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dirModel;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Dir $dirModel
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Dir $dirModel,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
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
        $this->_init('Magento_Downloadable_Model_Resource_Sample');
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
     * @return Magento_Downloadable_Model_Sample
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
        return $this->_dirModel->getDir(Magento_Core_Model_Dir::MEDIA)
            . DS . 'downloadable' . DS . 'tmp' . DS . 'samples';
    }

    /**
     * Retrieve sample files path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->_dirModel->getDir(Magento_Core_Model_Dir::MEDIA)
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
