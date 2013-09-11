<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward resource setup model
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * Current entity type id
     *
     * @var string
     */
    protected $_currentEntityTypeId;

    /**
     * Add attribute to an entity type
     * If attribute is system will add to all existing attribute sets
     *
     * @param string|integer $entityTypeId
     * @param string $code
     * @param array $attr
     * @return \Magento\Eav\Model\Entity\Setup
     */
    public function addAttribute($entityTypeId, $code, array $attr)
    {
        $this->_currentEntityTypeId = $entityTypeId;
        return parent::addAttribute($entityTypeId, $code, $attr);
    }

    /**
     * Prepare attribute values to save
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        if ($this->_currentEntityTypeId == 'customer') {
            $data = array_merge($data, array(
                'is_visible'                => $this->_getValue($attr, 'visible', 1),
                'is_visible_on_front'       => $this->_getValue($attr, 'visible_on_front', 0),
                'input_filter'              => $this->_getValue($attr, 'input_filter', ''),
                'lines_to_divide_multiline' => $this->_getValue($attr, 'lines_to_divide', 0),
                'min_text_length'           => $this->_getValue($attr, 'min_text_length', 0),
                'max_text_length'           => $this->_getValue($attr, 'max_text_length', 0)
            ));
        }
        return $data;
    }
}
