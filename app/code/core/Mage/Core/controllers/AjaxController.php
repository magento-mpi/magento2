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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_AjaxController extends Mage_Core_Controller_Front_Action {

    public function translateAction()
    {
        if (!Mage::getStoreConfigFlag('dev/locale/translate_inline')) {
            return;
        }
        if (!$translate = $this->getRequest()->getPost('translate')) {
            return;
        }

        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $table = Mage::getSingleton('core/resource')->getTableName('core/translate');
        $storeId = Mage::app()->getStore()->getId();

        foreach ($translate as $t) {
            $select = $write->select()
                ->from($table, array('key_id', 'translate'))
                ->where('string=?', $t['original'])
                ->where('store_id=?', $storeId);
            if ($row = $write->fetchRow($select)) {
                if ($row['translate']!=$t['custom']) {
                    $write->update($table, array('translate'=>$t['custom']), 'key_id='.(int)$row['key_id']);
                }
            } else {
                $write->insert($table, array('string'=>$t['original'], 'store_id'=>$storeId, 'translate'=>$t['custom']));
            }
        }

//        $model = Mage::getModel('core/translate_string')
//            ->setStoreId(Mage::app()->getStore()->getId());
//
//        foreach ($translate as $t) {
//            $model->unsKeyId()->load($t['original']);
//            $model->setString($t['original'])
//                ->setTranslate($t['custom'])
//                ->save();
//        }
        exit;
    }
}
