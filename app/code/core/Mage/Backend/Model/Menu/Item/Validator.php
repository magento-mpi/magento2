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
        'acl', 'objectFactory', 'urlModel', 'id', 'title', 'module'
    );

    /**
     * The list of required param types
     *
     * @var array
     */
    protected $_requiredTypes = array(
        'acl' => 'Mage_Backend_Model_Auth_Session',
        'objectFactory' => 'Mage_Core_Model_Config',
        'urlModel' => 'Mage_Backend_Model_Url',
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

        $textValidator = new Zend_Validate_StringLength(array('min' => 3, 'max' => 50));

        $this->_validators['id'] = $idValidator;
        $this->_validators['title'] = $textValidator;
        $this->_validators['action'] = $idValidator;
        $this->_validators['resource'] = $idValidator;
        $this->_validators['toolTip'] = $textValidator;
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
        $this->_ids[] = $data['id'];

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
    }

    public function validateParam($param, $value)
    {
        if (isset($this->_required[$param])){
            throw new InvalidArgumentException('Parameter ' . $param . ' is required');
        }
        if (isset($this->_requiredTypes[$param]) && !($value instanceof $this->_requiredTypes[$param])) {
            throw new InvalidArgumentException(
                'Wrong param ' . $param . ': Expected ' . $this->_requiredTypes[$param] . ', received '
                    . get_class($value)
            );
        }
        if (isset($this->_validators[$param]) && !$this->_validators[$param]->isValid($value)) {
            throw new InvalidArgumentException(
                "Param " . $param . " doesn't pass validation: "
                    . implode('; ', $this->_validators[$param]->getMessages())
            );
        }
    }
}
