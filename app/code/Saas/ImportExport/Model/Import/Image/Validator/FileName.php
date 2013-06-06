<?php
/**
 * Image Validator FileName
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Import_Image_Validator_FileName extends Magento_Validator_ValidatorAbstract
{
    /**
     * @const string Error constants
     */
    const NAME_IS_WRONG = 'nameIsWrong';
    const NAME_LENGTH_TOO_BIG = 'nameLengthTooBig';

    /**
     * @var array Error message template
     */
    protected $_messageTemplates = array(
        self::NAME_IS_WRONG => 'File name error',
        self::NAME_LENGTH_TOO_BIG => 'File name is too long:',
    );

    /**
     * Length limit value
     *
     * @var int
     */
    protected $_lengthLimit;

    /**
     * Pattern value
     *
     * @var int
     */
    protected $_pattern;

    /**
     * Sets validator options
     *
     * @param  array|Zend_Config $options
     * @throws InvalidArgumentException
     */
    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (!isset($options['lengthLimit'])) {
            throw new InvalidArgumentException("Missing option 'lengthLimit'");
        }
        $this->setLengthLimit($options['lengthLimit']);

        if (!isset($options['pattern'])) {
            throw new InvalidArgumentException("Missing option 'pattern'");
        }
        $this->setPattern($options['pattern']);
    }

    /**
     * Returns the LengthLimit option
     *
     * @return int
     */
    public function getLengthLimit()
    {
        return $this->_lengthLimit;
    }

    /**
     * Sets the lengthLimit option
     *
     * @param  int $lengthLimit
     * @throws InvalidArgumentException
     * @return Saas_ImportExport_Model_Import_Image_Validator_FileName Provides a fluent interface
     */
    public function setLengthLimit($lengthLimit)
    {
        if (!is_numeric($lengthLimit) || $lengthLimit < 0) {
            throw new InvalidArgumentException("Wrong value for 'lengthLimit'");
        }
        $this->_lengthLimit = (int)$lengthLimit;

        return $this;
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * Sets the max option
     *
     * @param  string $pattern
     * @throws InvalidArgumentException
     * @return Saas_ImportExport_Model_Import_Image_Validator_FileName Provides a fluent interface
     */
    public function setPattern($pattern)
    {
        if (!is_string($pattern) || empty($pattern)) {
            throw new InvalidArgumentException("Wrong value for 'pattern'");
        }
        $this->_pattern = $pattern;

        return $this;
    }

    /**
     * Check file name
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_clearMessages();

        $value = basename($value);
        if (!preg_match($this->getPattern(), $value)) {
            $this->_addMessages(array($this->_messageTemplates[self::NAME_IS_WRONG]));
            return false;
        }

        $lengthLimit = $this->getLengthLimit();
        if ($lengthLimit && strlen($value) > $lengthLimit) {
            $this->_addMessages(array($this->_messageTemplates[self::NAME_LENGTH_TOO_BIG]));
            return false;
        }

        return true;
    }

    /**
     * Sets validation failure message templates given as an array, where the array keys are the message keys,
     * and the array values are the message template strings.
     *
     * @param  array $messages
     * @return Saas_ImportExport_Model_Import_Image_Validator_FileName
     */
    public function setMessages(array $messages)
    {
        foreach ($messages as $key => $message) {
            $this->_messageTemplates[$key] = $message;
        }
        return $this;
    }
}
