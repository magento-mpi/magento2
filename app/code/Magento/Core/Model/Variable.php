<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Custom variable model
 *
 * @method \Magento\Core\Model\Resource\Variable _getResource()
 * @method \Magento\Core\Model\Resource\Variable getResource()
 * @method string getCode()
 * @method \Magento\Core\Model\Variable setCode(string $value)
 * @method string getName()
 * @method \Magento\Core\Model\Variable setName(string $value)
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model;

class Variable extends \Magento\Core\Model\AbstractModel
{
    const TYPE_TEXT = 'text';
    const TYPE_HTML = 'html';

    protected $_storeId = 0;

    /**
     * Internal Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Core\Model\Resource\Variable');
    }

    /**
     * Setter
     *
     * @param integer $storeId
     * @return \Magento\Core\Model\Variable
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Load variable by code
     *
     * @param string $code
     * @return \Magento\Core\Model\Variable
     */
    public function loadByCode($code)
    {
        $this->getResource()->loadByCode($this, $code);
        return $this;
    }

    /**
     * Return variable value depend on given type
     *
     * @param string $type
     * @return string
     */
    public function getValue($type = null)
    {
        if ($type === null) {
            $type = self::TYPE_HTML;
        }
        if ($type == self::TYPE_TEXT || !(strlen((string)$this->getData('html_value')))) {
            $value = $this->getData('plain_value');
            //escape html if type is html, but html value is not defined
            if ($type == self::TYPE_HTML) {
                $value = nl2br(\Mage::helper('Magento\Core\Helper\Data')->escapeHtml($value));
            }
            return $value;
        }
        return $this->getData('html_value');
    }

    /**
     * Validation of object data. Checking for unique variable code
     *
     * @return boolean | string
     */
    public function validate()
    {
        if ($this->getCode() && $this->getName()) {
            $variable = $this->getResource()->getVariableByCode($this->getCode());
            if (!empty($variable) && $variable['variable_id'] != $this->getId()) {
                return __('Variable Code must be unique.');
            }
            return true;
        }
        return __('Validation has failed.');
    }

    /**
     * Retrieve variables option array
     *
     * @param boolean $withValues
     * @return array
     */
    public function getVariablesOptionArray($withGroup = false)
    {
        /* @var $collection \Magento\Core\Model\Resource\Variable\Collection */
        $collection = $this->getCollection();
        $variables = array();
        foreach ($collection->toOptionArray() as $variable) {
            $variables[] = array(
                'value' => '{{customVar code=' . $variable['value'] . '}}',
                'label' => __('%1', $variable['label'])
            );
        }
        if ($withGroup && $variables) {
            $variables = array(
                'label' => __('Custom Variables'),
                'value' => $variables
            );
        }
        return $variables;
    }

}
