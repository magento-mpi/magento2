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
 * @category   Mage
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating entity resource
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Rating_Model_Mysql4_Rating_Entity extends Mage_Core_Model_Mysql4_Abstract
{
    function _construct()
    {
        $this->_init('rating/rating_entity', 'entity_id');
    }

    public function getIdByCode($entityCode)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getTable('rating_entity'), $this->getIdFieldName())
            ->where('entity_code = ?', $entityCode);
        return $read->fetchOne($select);
    }
}