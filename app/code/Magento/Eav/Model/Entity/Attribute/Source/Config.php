<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Entity/Attribute/Model - attribute selection source from configuration
 *
 * this class should be abstract, but kept usual for legacy purposes
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Entity\Attribute\Source;

class Config extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var array
     */
    protected $_optionsData;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->_optionsData = $options;
    }

    /**
     * Retrieve all options for the source from configuration
     *
     * @throws \Magento\Eav\Exception
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = array();

            if (empty($this->_optionsData)) {
                throw new \Magento\Eav\Exception(__('No options found.'));
            }
            foreach ($this->_optionsData as $option) {
                $this->_options[] = array(
                    'value' => $option['value'],
                    'label' => __($option['label'])
                );
            }
        }

        return $this->_options;
    }
}
