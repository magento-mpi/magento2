<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * RMA Item Dynamic attributes Form Block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Form extends Enterprise_Eav_Block_Form
{
    /**
     * Name of the block in layout update xml file
     *
     * @var string
     */
    protected $_xmlBlockName = 'enterprise_rma_item_form_template';

    /**
     * Class path of Form Model
     *
     * @var string
     */
    protected $_formModelPath = 'enterprise_rma/item_form';

    /**
     * Parses quantity depending on isQtyDecimal flag
     *
     * @param float $quantity
     * @param Enterprise_Rma_Model_Item $item
     * @return int|float
     */
    public function parseQuantity($quantity, $item)
    {
        if (is_null($quantity)) {
             $quantity = $item->getOrigData('qty_requested');
        }
        if ($item->getIsQtyDecimal()) {
            return sprintf("%01.4f", $quantity);
        } else {
            return intval($quantity);
        }
    }

    /**
     * Get Qty by status
     *
     * @param Enterprise_Rma_Model_Item $item
     * @return int|float
     */
    public function getQty($item)
    {
        $qty = $item->getQtyRequested();

        if ($item->getQtyApproved()
            && ($item->getStatus() == Enterprise_Rma_Model_Rma_Source_Status::STATE_APPROVED)
        ) {
            $qty = $item->getQtyApproved();
        } elseif ($item->getQtyReturned()
            && ($item->getStatus() == Enterprise_Rma_Model_Rma_Source_Status::STATE_RECEIVED)
        ) {
            $qty = $item->getQtyReturned();
        } elseif ($item->getQtyAuthorized()
            && ($item->getStatus() == Enterprise_Rma_Model_Rma_Source_Status::STATE_AUTHORIZED)
        ) {
            $qty = $item->getQtyAuthorized();
        }

        return $this->parseQuantity($qty, $item);
    }
}
