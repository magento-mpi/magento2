<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Resource;

/**
 * Tax class resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class TaxClass extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Unique tax class and tax type title. Also used for error message text on save.
     */
    const UNIQUE_TAX_CLASS_MSG = 'Class name and class type';

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('tax_class', 'class_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => array('class_type', 'class_name'),
                'title' => __(
                    self::UNIQUE_TAX_CLASS_MSG
                )
            )
        );
        return $this;
    }
}
