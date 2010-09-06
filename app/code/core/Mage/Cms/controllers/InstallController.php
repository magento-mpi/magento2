<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Cms index controller
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_InstallController extends Mage_Core_Controller_Front_Action
{
    /**
     * Return Core Resource singleton
     *
     * @return Mage_Core_Model_Resource
     */
    protected function _getResource()
    {
        return Mage::getSingleton('core/resource');
    }

    /**
     * Return database connection
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getConnection()
    {
        return $this->_getResource()->getConnection('core_write');
    }

    /**
     * Return array of default entity type properties
     *
     * @return array
     */
    protected function _getEntityDefaultValues()
    {
        return array(
            'entity_model'                  => null,
            'attribute_model'               => null,
            'entity_table'                  => null,
            'increment_model'               => null,
            'increment_per_store'           => false,
            'additional_attribute_table'    => null,
            'entity_attribute_collection'   => null,
        );
    }

    /**
     * Return array of default customer attribute properties
     *
     * @return array
     */
    protected function _getCustomerAttributeDefaultValues()
    {
        return $this->_getEavAttributeDefaultValues() + array(
            'visible'           => true,
            'system'            => true,
            'input_filter'      => null,
            'multiline_count'   => 0,
            'validate_rules'    => null,
            'data'              => null,
            'position'          => 0
        );
    }

    /**
     * Return array of default eav attribute properties
     *
     * @return array
     */
    protected function _getEavAttributeDefaultValues()
    {
        return array(
            'type'              => null,
            'label'             => null,
            'input'             => null,
            'frontend'          => null,
            'source'            => null,
            'backend'           => null,
            'table'             => null,
            'frontend_class'    => null,
            'required'          => true,
            'user_defined'      => false,
            'unique'            => false,
            'default'           => null,
            'note'              => null,
            'sort_order'        => 0
        );
    }

    /**
     * Return array of default catalog attribute properties
     *
     * @return array
     */
    protected function _getCatalogAttributeDefaultValues()
    {
        return $this->_getEavAttributeDefaultValues() + array(
            'input_renderer'                => null,
            'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible'                       => true,
            'searchable'                    => false,
            'filterable'                    => false,
            'comparable'                    => false,
            'visible_on_front'              => false,
            'wysiwyg_enabled'               => false,
            'is_html_allowed_on_front'      => false,
            'visible_in_advanced_search'    => false,
            'filterable_in_search'          => false,
            'used_in_product_listing'       => false,
            'used_for_sort_by'              => false,
            'apply_to'                      => false,
            'position'                      => false,
            'is_configurable'               => true,
            'used_for_promo_rules'          => false,

            'group'                         => 'General'
        );
    }

    public function customerSetupAction()
    {
        $resource = $this->_getResource();
        $adapter  = $this->_getConnection();

        $entityTypeCode = $this->getRequest()->getParam('entity_type', 'customer');
        $entityType = Mage::getSingleton('eav/config')->getEntityType($entityTypeCode);

        $output  = '<' . '?php' . "\n\n";
        $output .= '$entities = array(' . "\n";
        $output .= sprintf("    %-32s=> array(\n", sprintf("'%s'", $entityType->getEntityTypeCode()));

        foreach ($this->_getEntityDefaultValues() as $k => $v) {
            $value = $entityType->getData($k);
            if ($value != '0' && empty($value)) {
                continue;
            }
            if (is_bool($v)) {
                $value = (bool)$value;
            }
            if ($v === $value) {
                continue;
            }
            if ($k == 'entity_table') {
                $k = 'table';
            }
            $output .= sprintf("        %-32s=> %s,\n", sprintf("'%s'", $k), var_export($value, true));
        }

        $output .= "        'attributes'                    => array(\n";

        $defaultAttributeValues = $this->_getCustomerAttributeDefaultValues();

        $select = $adapter->select()
            ->from(array('ea' => $resource->getTableName('eav/attribute')))
            ->join(
                array('cea' => $resource->getTableName('customer/eav_attribute')),
                'ea.attribute_id = cea.attribute_id'
                )
            ->join(
                array('eas' => $resource->getTableName('eav/attribute_set')),
                'ea.entity_type_id = eas.entity_type_id AND eas.attribute_set_name = :set_name',
                array())
            ->joinLeft(
                array('eea' => $resource->getTableName('eav/entity_attribute')),
                'eea.entity_type_id = ea.entity_type_id AND eas.attribute_set_id = eea.attribute_set_id AND ea.attribute_id = eea.attribute_id',
                array('entity_order' => 'sort_order'))
            ->where('ea.entity_type_id = ?', (int) $entityType->getId())
            ->order('ea.attribute_id ASC');

        foreach ($adapter->fetchAll($select, array('set_name' => 'Default')) as $row) {
            $attributeCode = sprintf("'%s'", $row['attribute_code']);
            $attribute = array(
                // default
                'type'              => $row['backend_type'],
                'label'             => $row['frontend_label'],
                'input'             => $row['frontend_input'],
                'frontend'          => $row['frontend_model'],
                'source'            => $row['source_model'],
                'backend'           => $row['backend_model'],
                'table'             => $row['backend_table'],
                'frontend_class'    => $row['frontend_class'],
                'required'          => (bool)$row['is_required'],
                'user_defined'      => (bool)$row['is_user_defined'],
                'unique'            => (bool)$row['is_unique'],
                'note'              => $row['note'],
                'sort_order'        => (int)$row['entity_order'],
                // customer
                'visible'           => (bool)$row['is_visible'],
                'system'            => (bool)$row['is_system'],
                'input_filter'      => $row['input_filter'],
                'multiline_count'   => (int)$row['multiline_count'],
                'validate_rules'    => $row['validate_rules'],
                'data'              => $row['data_model'],
                'position'          => (int)$row['sort_order']
            );

            $output .= sprintf("%s%-20s=> array(\n", str_repeat(' ', 12), $attributeCode);
            foreach ($attribute as $k => $v) {
                if ($v != '0' && empty($v)) {
                    continue;
                }
                if ($defaultAttributeValues[$k] === $v) {
                    continue;
                }
                $output .= sprintf("%s%-20s=> %s,\n", str_repeat(' ', 16), sprintf("'%s'", $k), var_export($v, true));
            }
            $output .= "            ),\n";
        }
        $output .= "        )\n";
        $output .= "   )\n);";

        highlight_string($output);
    }

    public function catalogSetupAction()
    {
        $resource = $this->_getResource();
        $adapter  = $this->_getConnection();

        $entityTypeCode = $this->getRequest()->getParam('entity_type', 'catalog_product');
        $entityType = Mage::getSingleton('eav/config')->getEntityType($entityTypeCode);

        $output  = '<' . '?php' . "\n\n";
        $output .= '$entities = array(' . "\n";
        $output .= sprintf("    %-32s=> array(\n", sprintf("'%s'", $entityType->getEntityTypeCode()));

        foreach ($this->_getEntityDefaultValues() as $k => $v) {
            $value = $entityType->getData($k);
            if ($value != '0' && empty($value)) {
                continue;
            }
            if (is_bool($v)) {
                $value = (bool)$value;
            }
            if ($v === $value) {
                continue;
            }
            if ($k == 'entity_table') {
                $k = 'table';
            }
            $output .= sprintf("        %-32s=> %s,\n", sprintf("'%s'", $k), var_export($value, true));
        }

        $output .= "        'attributes'                    => array(\n";

        $defaultAttributeValues = $this->_getCatalogAttributeDefaultValues();

        $select = $adapter->select()
            ->from(array('ea' => $resource->getTableName('eav/attribute')))
            ->join(
                array('cea' => $resource->getTableName('catalog/eav_attribute')),
                'ea.attribute_id = cea.attribute_id'
                )
            ->join(
                array('eas' => $resource->getTableName('eav/attribute_set')),
                'ea.entity_type_id = eas.entity_type_id AND eas.attribute_set_name = :set_name',
                array())
            ->joinLeft(
                array('eea' => $resource->getTableName('eav/entity_attribute')),
                'eea.entity_type_id = ea.entity_type_id AND eas.attribute_set_id = eea.attribute_set_id AND ea.attribute_id = eea.attribute_id',
                array('entity_order' => 'sort_order'))
            ->joinLeft(
                array('eag' => $resource->getTableName('eav/attribute_group')),
                'eea.attribute_group_id = eag.attribute_group_id',
                array('attribute_group_name'))
            ->where('ea.entity_type_id = ?', (int) $entityType->getId())
            ->order('ea.attribute_id ASC');

        foreach ($adapter->fetchAll($select, array('set_name' => 'Default')) as $row) {
            $attributeCode = sprintf("'%s'", $row['attribute_code']);
            $attribute = array(
                // default
                'type'              => $row['backend_type'],
                'label'             => $row['frontend_label'],
                'input'             => $row['frontend_input'],
                'frontend'          => $row['frontend_model'],
                'source'            => $row['source_model'],
                'backend'           => $row['backend_model'],
                'table'             => $row['backend_table'],
                'frontend_class'    => $row['frontend_class'],
                'required'          => (bool)$row['is_required'],
                'user_defined'      => (bool)$row['is_user_defined'],
                'unique'            => (bool)$row['is_unique'],
                'default'           => $row['default_value'],
                'note'              => $row['note'],
                'sort_order'        => (int)$row['entity_order'],
                // catalog
                'input_renderer'                => $row['frontend_input_renderer'],
                'global'                        => (int)$row['is_global'],
                'visible'                       => (bool)$row['is_visible'],
                'searchable'                    => (bool)$row['is_searchable'],
                'filterable'                    => (bool)$row['is_filterable'],
                'comparable'                    => (bool)$row['is_comparable'],
                'visible_on_front'              => (bool)$row['is_visible_on_front'],
                'wysiwyg_enabled'               => (bool)$row['is_wysiwyg_enabled'],
                'is_html_allowed_on_front'      => (bool)$row['is_html_allowed_on_front'],
                'visible_in_advanced_search'    => (bool)$row['is_visible_in_advanced_search'],
                'filterable_in_search'          => (bool)$row['is_filterable_in_search'],
                'used_in_product_listing'       => (bool)$row['used_in_product_listing'],
                'used_for_sort_by'              => (bool)$row['used_for_sort_by'],
                'apply_to'                      => $row['apply_to'],
                'position'                      => (bool)$row['position'],
                'is_configurable'               => (bool)$row['is_configurable'],
                'used_for_promo_rules'          => (bool)$row['is_used_for_promo_rules'],

                'group'                         => $row['attribute_group_name'],
            );

            $output .= sprintf("%s%-20s=> array(\n", str_repeat(' ', 12), $attributeCode);
            foreach ($attribute as $k => $v) {
                if ($v != '0' && empty($v)) {
                    continue;
                }
                if ($defaultAttributeValues[$k] === $v) {
                    continue;
                }
                if ($k == 'apply_to') {
                    $v = explode(',', $v);
                    if (false !== ($i = array_search('bundle', $v))) {
                        unset($v[$i]);
                    }
                    if (false !== ($i = array_search('downloadable', $v))) {
                        unset($v[$i]);
                    }
                    if (empty($v)) {
                        continue;
                    }
                    $value = var_export(implode(',', $v), true);
                } else if ($k == 'global') {
                    switch($v) {
                        case Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE:
                            $value = 'Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE';
                            break;
                        case Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL:
                            $value = 'Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL';
                            break;
                        case Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE:
                            $value = 'Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE';
                            break;
                    }
                } else {
                    $value = var_export($v, true);
                }
                $output .= sprintf("%s%-32s=> %s,\n", str_repeat(' ', 16), sprintf("'%s'", $k), $value);
            }
            $output .= "            ),\n";
        }
        $output .= "        )\n";
        $output .= "   )\n);";

        highlight_string($output);
    }

    public function adminUserAction()
    {
        $model = Mage::getModel('admin/user')->loadByUsername('admin');
        if (!$model->getId()) {
            $model->addData(array(
                'firstname'     => 'Store',
                'lastname'      => 'Owner',
                'email'         => 'user@magentocommerce.com',
                'username'      => 'admin',
                'new_password'  => '123123q'
                ))
                ->save()
                ->setRoleIds(array(1))
                ->saveRelations();
            echo "admin user was created successfuly";
        } else {
            $model->setNewPassword('123123q')
                ->save()
                ->setRoleIds(array(1))
                ->saveRelations();
            echo "admin user is exists";
        }
    }

    public function randAction()
    {
        $adapter = $this->_getConnection();
        $select  = $adapter->select()
            ->from($this->_getResource()->getTableName('core/resource'))
            ->orderRand();
        echo '<pre>';
        echo $select;
        echo '</pre>';

        // dummy fetch
        $adapter->fetchAll($select);

        $select  = $adapter->select()
            ->from($this->_getResource()->getTableName('cms/page'))
            ->orderRand('page_id');

        echo '<pre>';
        echo $select;
        echo '</pre>';

        // dummy fetch
        $adapter->fetchAll($select);
    }
}
