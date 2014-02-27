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
 * String translation model
 *
 * @method \Magento\Core\Model\Resource\Translate\String _getResource()
 * @method \Magento\Core\Model\Resource\Translate\String getResource()
 * @method int getStoreId()
 * @method \Magento\Core\Model\Translate\String setStoreId(int $value)
 * @method string getTranslate()
 * @method \Magento\Core\Model\Translate\String setTranslate(string $value)
 * @method string getLocale()
 * @method \Magento\Core\Model\Translate\String setLocale(string $value)
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Translate;

class String extends \Magento\Core\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Resource\Translate\String');
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
