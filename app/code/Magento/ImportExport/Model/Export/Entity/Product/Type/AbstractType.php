<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Export\Entity\Product\Type;

use Magento\Catalog\Model\Resource\Eav\Attribute;

/**
 * Export entity product type abstract model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractType
{
    /**
     * Overridden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = array();

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var string[]
     */
    protected $_disabledAttrs = array();

    /**
     * Attributes with index (not label) value.
     *
     * @var string[]
     */
    protected $_indexValueAttributes = array();

    /**
     * Return disabled attributes codes.
     *
     * @return string[]
     */
    public function getDisabledAttrs()
    {
        return $this->_disabledAttrs;
    }

    /**
     * Get attribute codes with index (not label) value.
     *
     * @return string[]
     */
    public function getIndexValueAttributes()
    {
        return $this->_indexValueAttributes;
    }

    /**
     * Additional check for model availability. If method returns FALSE - model is not suitable for data processing.
     *
     * @return bool
     */
    public function isSuitable()
    {
        return true;
    }

    /**
     * Add additional data to attribute.
     *
     * @param Attribute $attribute
     * @return bool
     */
    public function overrideAttribute(Attribute $attribute)
    {
        if (!empty($this->_attributeOverrides[$attribute->getAttributeCode()])) {
            $data = $this->_attributeOverrides[$attribute->getAttributeCode()];

            if (isset($data['options_method']) && method_exists($this, $data['options_method'])) {
                $data['filter_options'] = $this->{$data['options_method']}();
            }
            $attribute->addData($data);

            return true;
        }
        return false;
    }
}
