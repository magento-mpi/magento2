<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Item Form Model
 */
namespace Magento\Rma\Model\Item;

class Form extends \Magento\Eav\Model\Form
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'Magento_Rma';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'rma_item';

    /**
     * @var \Magento\Rma\Model\Resource\Item\Form\Attribute\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param \Magento\Eav\Model\AttributeDataFactory $attrDataFactory
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param \Magento\Core\Controller\Request\Http $httpRequest
     * @param \Magento\Validator\ConfigFactory $validatorConfigFactory
     * @param \Magento\Rma\Model\Resource\Item\Form\Attribute\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        \Magento\Eav\Model\AttributeDataFactory $attrDataFactory,
        \Magento\Validator\UniversalFactory $universalFactory,
        \Magento\Core\Controller\Request\Http $httpRequest,
        \Magento\Validator\ConfigFactory $validatorConfigFactory,
        \Magento\Rma\Model\Resource\Item\Form\Attribute\CollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct(
            $storeManager,
            $eavConfig,
            $modulesReader,
            $attrDataFactory,
            $universalFactory,
            $httpRequest,
            $validatorConfigFactory
        );
    }

    /**
     * Get EAV Entity Form Attribute Collection
     *
     * @return \Magento\Rma\Model\Resource\Item\Form\Attribute\Collection
     */
    protected function _getFormAttributeCollection()
    {
        return $this->_collectionFactory->create();
    }

    /**
     * Validate data array and return true or array of errors
     *
     * @param array $data
     * @return boolean|array
     */
    public function validateData(array $data)
    {
        $errors = array();
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getAttributeCode() == 'reason_other') {
                continue;
            }
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = null;
            }
            $result = $dataModel->validateValue($data[$attribute->getAttributeCode()]);
            if ($result !== true) {
                $errors = array_merge($errors, $result);
            }
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }
}
