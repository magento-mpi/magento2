<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model;

/**
 * EAV Entity Attribute Data Factory
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class AttributeDataFactory
{
    const OUTPUT_FORMAT_JSON = 'json';

    const OUTPUT_FORMAT_TEXT = 'text';

    const OUTPUT_FORMAT_HTML = 'html';

    const OUTPUT_FORMAT_PDF = 'pdf';

    const OUTPUT_FORMAT_ONELINE = 'oneline';

    const OUTPUT_FORMAT_ARRAY = 'array';

    // available only for multiply attributes

    /**
     * Array of attribute data models by input type
     *
     * @var array
     */
    protected $_dataModels = array();

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Stdlib\String $string
     */
    public function __construct(\Magento\ObjectManager $objectManager, \Magento\Stdlib\String $string)
    {
        $this->_objectManager = $objectManager;
        $this->string = $string;
    }

    /**
     * Return attribute data model by attribute
     * Set entity to data model (need for work)
     *
     * @param \Magento\Eav\Model\Attribute $attribute
     * @param \Magento\Core\Model\AbstractModel $entity
     * @return \Magento\Eav\Model\Attribute\Data\AbstractData
     */
    public function create(\Magento\Eav\Model\Attribute $attribute, \Magento\Core\Model\AbstractModel $entity)
    {
        /* @var $dataModel \Magento\Eav\Model\Attribute\Data\AbstractData */
        $dataModelClass = $attribute->getDataModel();
        if (!empty($dataModelClass)) {
            if (empty($this->_dataModels[$dataModelClass])) {
                $dataModel = $this->_objectManager->create($dataModelClass);
                $this->_dataModels[$dataModelClass] = $dataModel;
            } else {
                $dataModel = $this->_dataModels[$dataModelClass];
            }
        } else {
            if (empty($this->_dataModels[$attribute->getFrontendInput()])) {
                $dataModelClass = sprintf(
                    'Magento\Eav\Model\Attribute\Data\%s',
                    $this->string->upperCaseWords($attribute->getFrontendInput())
                );
                $dataModel = $this->_objectManager->create($dataModelClass);
                $this->_dataModels[$attribute->getFrontendInput()] = $dataModel;
            } else {
                $dataModel = $this->_dataModels[$attribute->getFrontendInput()];
            }
        }

        $dataModel->setAttribute($attribute);
        $dataModel->setEntity($entity);

        return $dataModel;
    }
}
