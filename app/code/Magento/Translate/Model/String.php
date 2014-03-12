<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * String translation model
 *
 * @method \Magento\Translate\Model\Resource\String _getResource()
 * @method \Magento\Translate\Model\Resource\String getResource()
 * @method int getStoreId()
 * @method \Magento\Translate\Model\String setStoreId(int $value)
 * @method string getTranslate()
 * @method \Magento\Translate\Model\String setTranslate(string $value)
 * @method string getLocale()
 * @method \Magento\Translate\Model\String setLocale(string $value)
 */
namespace Magento\Translate\Model;

class String extends \Magento\Core\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Translate\Model\Resource\String');
    }

    /**
     * @param string $string
     * @return $this
     */
    public function setString($string)
    {
        $this->setData('string', $string);
        return $this;
    }

    /**
     * Retrieve string
     *
     * @return string
     */
    public function getString()
    {
        return $this->getData('string');
    }
}
