<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Directory\Model\Resource\Country;

/**
 * \Directory country format resource model
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Format extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_country_format', 'country_format_id');
    }

    /**
     * Initialize unique fields
     *
     * @return \Magento\Directory\Model\Resource\Country\Format
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => array('country_id', 'type'),
                'title' => __('Country and Format Type combination should be unique')
            )
        );
        return $this;
    }
}
