<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Validate OAuth keys
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Model_Consumer_Validator_KeyLength extends Zend_Validate_StringLength
{
    /**
     * Key name
     *
     * @var string
     */
    protected $_name = 'Key';

    /**
     * Sets validator options
     *
     * @param  integer|array|Zend_Config $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $options     = func_get_args();
            if (!isset($options[1])) {
                $options[1] = 'utf-8';
            }
            parent::__construct($options[0], $options[0], $options[1]);
            return;
        } else {
            if (isset($options['length'])) {
                $options['max'] =
                $options['min'] = $options['length'];
            }
            if (isset($options['name'])) {
                $this->_name = $options['name'];
            }
        }
        parent::__construct($options);
    }

    /**
     * Init validation failure message template definitions
     *
     * @return Magento_Oauth_Model_Consumer_Validator_KeyLength
     */
    protected function _initMessageTemplates()
    {
        $_messageTemplates[self::TOO_LONG] =
            Mage::helper('Magento_Oauth_Helper_Data')->__("%name% '%value%' is too long. It must has length %min% symbols.");
        $_messageTemplates[self::TOO_SHORT] =
            Mage::helper('Magento_Oauth_Helper_Data')->__("%name% '%value%' is too short. It must has length %min% symbols.");

        return $this;
    }

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageVariables = array(
        'min'  => '_min',
        'max'  => '_max',
        'name' => '_name'
    );

    /**
     * Set length
     *
     * @param $length
     * @return Magento_Oauth_Model_Consumer_Validator_KeyLength
     */
    public function setLength($length)
    {
        parent::setMax($length);
        parent::setMin($length);
        return $this;
    }

    /**
     * Set length
     *
     * @return int
     */
    public function getLength()
    {
        return parent::getMin();
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if the string length of $value is at least the min option and
     * no greater than the max option (when the max option is not null).
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $result = parent::isValid($value);
        if (!$result && isset($this->_messages[self::INVALID])) {
            throw new Exception($this->_messages[self::INVALID]);
        }
        return $result;
    }

    /**
     * Set key name
     *
     * @param string $name
     * @return Magento_Oauth_Model_Consumer_Validator_KeyLength
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get key name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}
