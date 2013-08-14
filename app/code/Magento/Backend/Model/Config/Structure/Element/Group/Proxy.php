<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_Group_Proxy
    extends Magento_Backend_Model_Config_Structure_Element_Group
{
    /**
     * Object manager
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Backend_Model_Config_Structure_Element_Group
     */
    protected $_subject;

    /**
     * @param Magento_ObjectManager $objectManger
     */
    public function __construct(Magento_ObjectManager $objectManger)
    {
        $this->_objectManager = $objectManger;
    }

    /**
     * Retrieve subject
     *
     * @return Magento_Backend_Model_Config_Structure_Element_Group
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = $this->_objectManager->create('Magento_Backend_Model_Config_Structure_Element_Group');
        }
        return $this->_subject;
    }

    /**
     * Set element data
     *
     * @param array $data
     * @param string $scope
     */
    public function setData(array $data, $scope)
    {
        $this->_getSubject()->setData($data, $scope);
    }

    /**
     * Retrieve element id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_getSubject()->getId();
    }

    /**
     * Retrieve element label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_getSubject()->getLabel();
    }

    /**
     * Retrieve element label
     *
     * @return string
     */
    public function getComment()
    {
        return $this->_getSubject()->getComment();
    }

    /**
     * Retrieve frontend model class name
     *
     * @return string
     */
    public function getFrontendModel()
    {
        return $this->_getSubject()->getFrontendModel();
    }

    /**
     * Retrieve arbitrary element attribute
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return $this->_getSubject()->getAttribute($key);
    }

    /**
     * Check whether section is allowed for current user
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_getSubject()->isAllowed();
    }

    /**
     * Check whether element should be displayed
     *
     * @return bool
     */
    public function isVisible($websiteCode = '', $storeCode = '')
    {
        return $this->_getSubject()->isVisible($websiteCode, $storeCode);
    }

    /**
     * Retrieve css class of a tab
     *
     * @return string
     */
    public function getClass()
    {
        return $this->_getSubject()->getClass();
    }


    /**
     * Check whether element has visible child elements
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->_getSubject()->hasChildren();
    }

    /**
     * Retrieve children iterator
     *
     * @return Magento_Backend_Model_Config_Structure_Element_Iterator
     */
    public function getChildren()
    {
        return $this->_getSubject()->getChildren();
    }

    /**
     * Should group fields be cloned
     *
     * @return bool
     */
    public function shouldCloneFields()
    {
        return $this->_getSubject()->shouldCloneFields();
    }

    /**
     * Retrieve clone model
     *
     * @return Magento_Core_Model_Abstract
     */
    public function getCloneModel()
    {
        return $this->_getSubject()->getCloneModel();
    }

    /**
     * Populate form fieldset with group data
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     */
    public function populateFieldset(Magento_Data_Form_Element_Fieldset $fieldset)
    {
        $this->_getSubject()->populateFieldset($fieldset);
    }

    /**
     * Retrieve element data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_getSubject()->getData();
    }

    /**
     * Retrieve element path
     *
     * @param string $fieldPrefix
     * @return string
     */
    public function getPath($fieldPrefix = '')
    {
        return $this->_getSubject()->getPath($fieldPrefix);
    }

    /**
     * Check whether element should be expanded
     *
     * @return bool
     */
    public function isExpanded()
    {
        return $this->_getSubject()->isExpanded();
    }

    /**
     * Retrieve fieldset css
     *
     * @return string
     */
    public function getFieldsetCss()
    {
        return $this->_getSubject()->getFieldsetCss();
    }

    /**
     * Retrieve element dependencies
     *
     * @param string $storeCode
     * @return array
     */
    public function getDependencies($storeCode)
    {
        return $this->_getSubject()->getDependencies($storeCode);
    }
}

