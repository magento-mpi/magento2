<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Item_Validator
{
    /**
     * The list of required params
     *
     * @var array
     */
    protected $_required = array(
        'acl', 'appConfig', 'objectFactory', 'urlModel', 'storeConfig', 'id', 'title', 'module'
    );

    /**
     * The list of required param types
     *
     * @var array
     */
    protected $_requiredTypes = array(
        'acl' => 'Mage_Backend_Model_Auth_Session',
        'appConfig' => 'Mage_Core_Model_Config',
        'objectFactory' => 'Mage_Core_Model_Config',
        'urlModel' => 'Mage_Backend_Model_Url',
        'storeConfig' => 'Mage_Core_Model_Store_Config',
        'module' => 'Mage_Core_Helper_Abstract'
    );

    /**
     * List of created item ids
     *
     * @var array
     */
    protected $_ids = array();

    /**
     * The list of primitive validators
     *
     * @var Zend_Validate[]
     */
    protected $_validators = array();

    public function __construct()
    {
        $idValidator = new Zend_Validate();
        $idValidator->addValidator(new Zend_Validate_StringLength(array('min' => 3)));
        $idValidator->addValidator(new Zend_Validate_Regex('/^[A-Za-z0-9\/:_]+$/'));

        $attributeValidator = new Zend_Validate();
        $attributeValidator->addValidator(new Zend_Validate_StringLength(array('min' => 3)));
        $attributeValidator->addValidator(new Zend_Validate_Regex('/^[A-Za-z0-9\/_]+$/'));

        $textValidator = new Zend_Validate_StringLength(array('min' => 3, 'max' => 50));

        $titleValidator = $tooltipValidator = $textValidator;
        $actionValidator = $resourceValidator = $resourceValidator = $moduleDependencyValidator
            = $configDependencyValidator = $attributeValidator;

        $this->_validators['id'] = $idValidator;
        $this->_validators['title'] = $titleValidator;
        $this->_validators['action'] = $actionValidator;
        $this->_validators['resource'] = $resourceValidator;
        $this->_validators['dependsOnModule'] = $moduleDependencyValidator;
        $this->_validators['dependsOnConfig'] = $configDependencyValidator;
        $this->_validators['toolTip'] = $tooltipValidator;
    }
    /**
     * Validate menu item params
     *
     * @param $data
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function validate($data)
    {
        foreach ($this->_required as $param) {
            if (!isset($data[$param])) {
                throw new BadMethodCallException('Missing required param ' . $param);
            }
        }

        if (array_search($data['id'], $this->_ids) !== false) {
            throw new InvalidArgumentException('Item with id ' . $data ['id'] . ' already exists');
        }

        foreach ($data as $param => $value) {
            if (isset($this->_requiredTypes[$param]) && !($data[$param] instanceof $this->_requiredTypes[$param])) {
                throw new InvalidArgumentException(
                    'Wrong param ' . $param . ': Expected ' . $this->_requiredTypes[$param] . ', received '
                        . get_class($data[$param])
                );
            } elseif (!is_null($data[$param])
                && isset($this->_validators[$param])
                && !$this->_validators[$param]->isValid($value)
            ) {
                throw new InvalidArgumentException(
                    "Param " . $param . " doesn't pass validation: "
                        . implode('; ', $this->_validators[$param]->getMessages())
                );
            }
        }
        $this->_ids[] = $data['id'];
    }

    /**
     * Validate incoming param
     *
     * @param string $param
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function validateParam($param, $value)
    {
        if (in_array($param, $this->_required) && is_null($value)) {
            throw new InvalidArgumentException('Param ' . $param . ' is required');
        }

        if (!is_null($value) && isset($this->_validators[$param]) && !$this->_validators[$param]->isValid($value)) {
            throw new InvalidArgumentException(
                'Param ' . $param . ' doesn\'t pass validation: '
                    . implode('; ', $this->_validators[$param]->getMessages())
            );
        }
    }
}
