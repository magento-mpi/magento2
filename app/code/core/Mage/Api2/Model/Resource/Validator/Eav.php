<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 EAV Validator
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Validator_Eav extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Config node key of current validator
     */
    const CONFIG_NODE_KEY = 'eav';

    /**
     * Form path
     *
     * @var string
     */
    protected $_formPath;

    /**
     * Entity model
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_entity;

    /**
     * Form code
     *
     * @var string
     */
    protected $_formCode;

    /**
     * Eav form model
     *
     * @var Mage_Eav_Model_Form
     */
    protected $_eavForm;

    /**
     * Construct. Set all depends.
     *
     * Required parameteres for options:
     * - resource
     * - operation
     *
     * @param array $options
     * @throws Exception If passed parameter 'resource' is wrong
     * @throws Exception If passed parameter 'operation' is empty
     * @throws Exception If config parameter 'formPath' is empty
     * @throws Exception If config parameter 'formCode' is empty
     * @throws Exception If config parameter 'entity' is wrong
     * @throws Exception If entity is not model
     * @throws Exception If eav form is not found
     */
    public function __construct($options)
    {
        if (!isset($options['resource']) || !$options['resource'] instanceof Mage_Api2_Model_Resource) {
            throw new Exception("Passed parameter 'resource' is wrong.");
        }
        $resource = $options['resource'];
        $resourceType = $resource->getResourceType();
        $userType = $resource->getUserType();

        if (!isset($options['operation']) || empty($options['operation'])) {
            throw new Exception("Passed parameter 'operation' is empty.");
        }
        $operation = $options['operation'];

        $validationConfig = $resource->getConfig()->getValidationConfig($resource, self::CONFIG_NODE_KEY);

        if (!isset($validationConfig[$userType]['form_model'])) {
            throw new Exception("Config parameter 'formPath' is empty.");
        }
        $this->_formPath = $validationConfig[$userType]['form_model'];

        if (!isset($validationConfig[$userType]['form_code'])) {
            throw new Exception("Config parameter 'formCode' is empty.");
        }
        $this->_formCode = $validationConfig[$userType]['form_code'];

        if (!isset($validationConfig[$userType]['entity_model'])) {
            throw new Exception("Config parameter 'entity' is wrong.");
        }
        $this->_entity = Mage::getModel($validationConfig[$userType]['entity_model']);
        if (empty($this->_entity) || !$this->_entity instanceof Mage_Core_Model_Abstract) {
            throw new Exception("Entity is not model.");
        }

        $this->_eavForm = Mage::getModel($this->_formPath);
        if (empty($this->_eavForm) || !$this->_eavForm instanceof Mage_Eav_Model_Form) {
            throw new Exception("Eav form '{$this->_formPath}' is not found.");
        }
        $this->_eavForm->setEntity($this->_entity)
            ->setFormCode($this->_formCode)
            ->ignoreInvisible(false);
    }

    /**
     * Filter request data.
     *
     * @param  array $data
     * @return array Filtered data
     */
    public function filter(array $data)
    {
        return $this->_eavForm->extractData($this->_eavForm->prepareRequest($data));
    }

    /**
     * Validate entity.
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param  array $data
     * @return bool
     */
    public function isSatisfiedByData(array $data)
    {
        $errors = $this->_eavForm->validateData($data);
        if (true !== $errors) {
            $this->_setErrors($errors);
            return false;
        }
        return true;
    }
}
