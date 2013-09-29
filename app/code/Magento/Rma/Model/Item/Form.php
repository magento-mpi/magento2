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
     * @param \Magento\Rma\Model\Resource\Item\Form\Attribute\CollectionFactory $collectionFactory
     */
    public function __construct(\Magento\Rma\Model\Resource\Item\Form\Attribute\CollectionFactory $collectionFactory)
    {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct();
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
