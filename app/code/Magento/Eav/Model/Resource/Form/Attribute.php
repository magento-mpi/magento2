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
 * EAV Form Attribute Resource Model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Resource\Form;

abstract class Attribute extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Return form attribute IDs by form code
     *
     * @param string $formCode
     * @return array
     */
    public function getFormAttributeIds($formCode)
    {
        $bind   = array('form_code' => $formCode);
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'attribute_id')
            ->where('form_code = :form_code');

        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }
}
