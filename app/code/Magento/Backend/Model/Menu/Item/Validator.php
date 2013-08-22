<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Menu_Item_Validator
{
    /**
     * The list of required params
     *
     * @var array
     */
    protected $_required = array(
        'id', 'title', 'resource'
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

        $resourceValidator = new Zend_Validate();
        $resourceValidator->addValidator(new Zend_Validate_StringLength(array('min' => 8)));
        $resourceValidator->addValidator(
            new Zend_Validate_Regex('/^[A-Z][A-Za-z0-9]+_[A-Z][A-Za-z0-9]+::[A-Za-z_0-9]+$/')
        );

        $attributeValidator = new Zend_Validate();
        $attributeValidator->addValidator(new Zend_Validate_StringLength(array('min' => 3)));
        $attributeValidator->addValidator(new Zend_Validate_Regex('/^[A-Za-z0-9\/_]+$/'));

        $textValidator = new Zend_Validate_StringLength(array('min' => 3, 'max' => 50));

        $titleValidator = $tooltipValidator = $textValidator;
        $actionValidator = $moduleDepValidator = $configDepValidator = $attributeValidator;

        $this->_validators['id'] = $idValidator;
        $this->_validators['title'] = $titleValidator;
        $this->_validators['action'] = $actionValidator;
        $this->_validators['resource'] = $resourceValidator;
        $this->_validators['dependsOnModule'] = $moduleDepValidator;
        $this->_validators['dependsOnConfig'] = $configDepValidator;
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
            if (!is_null($data[$param])
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
