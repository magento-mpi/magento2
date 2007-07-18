<?php
/**
 * Customer account form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View extends Mage_Core_Block_Template
{
    const ONLINE_INTERVAL = 900; // 15 min
    protected $_customer;
    protected $_customerLog;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/customer/tab/view.phtml');
    }

    protected function _initChildren()
    {
        $salesAccordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->setId('salesAccordion');
        $salesAccordion->addItem('salesStat', array(
            'title'     => __('Sales statistics'),
            // @todo create block after sales realization
            'content'   => '<div class="grid"><table cellspacing="0" class="data">
				<thead>
					<tr>
						<th>Store</th>
						<th>Lifetime Sale</th>
						<th>Average Sale</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td class="label"><strong><big>Total</big></strong></td>
						<td class="emph"><strong><big>$2076.97</big></strong></td>
						<td class="emph"><strong><big>$116.74</big></strong></td>
					</tr>
				</tfoot>
				<tbody>
					<tr>
						<td class="label">Magento Store (EN)</td>
						<td>$1875.99</td>
						<td>$89.99</td>
					</tr>
					<tr class="even">
						<td class="label">Varien Store (EN)</td>
						<td>$200.98</td>
						<td>$26.75</td>
					</tr>
				</tbody>
			</table></div>',
            'open'      => true
        ));
        $this->setChild('salesAccordion', $salesAccordion);
        
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->setId('customerViewAccordion')
            //->setShowOnlyOne(0)
            ;

        /* @var $accordion Mage_Adminhtml_Block_Widget_Accordion */
        $accordion->addItem('lastOrders', array(
            'title'     => __('Last %s Orders', 5),
            'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_view_orders')->setId('last5orders'),
            'open'      => true
        ));

        $accordion->addItem('shopingCart', array(
            'title'         => __('Shopping Cart'),
            //'content_url'   => Mage::getBaseUrl(),
            //'ajax'          => true,
            'content'       => 'cart'
        ));

        $accordion->addItem('wishlist', array(
            'title'     => __('Wishlist'),
            'content'   => 'Wishlist'
        ));
        $this->setChild('accordion', $accordion);
    }

    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::registry('customer');
        }
        return $this->_customer;
    }
    
    public function getCustomerLog()
    {
        if (!$this->_customerLog) {
            $this->_customerLog = Mage::getModel('log/customer')
                ->load($this->getCustomer()->getId());
            
        }
        return $this->_customerLog;
    }
    
    public function getFormat()
    {
    	return $this->_dateTimeFormat;
    }

    public function getCreateDate()
    {
        return strftime(Mage::getStoreConfig('general/local/datetime_format_medium'), strtotime($this->getCustomer()->getCreatedAt()));
    }

    public function getLastLoginDate()
    {
        if ($date = $this->getCustomerLog()->getLoginAt()) {
            return strftime(Mage::getStoreConfig('general/local/datetime_format_medium'), strtotime($date));
        }
        return __('Never');
    }

    public function getCurrentStatus()
    {
        $log = $this->getCustomerLog();
        if ($log->getLogoutAt() || strtotime(now())-strtotime($log->getLastVisitAt())>self::ONLINE_INTERVAL) {
            return __('Offline');
        }
        return __('Online');
    }
    
    public function getCreatedInStore()
    {
        return Mage::getModel('core/store')->load($this->getCustomer()->getStoreId())->getName();
    }

    public function getBillingAddressHtml()
    {
        $html = '';
        if ($address = $this->getCustomer()->getPrimaryBillingAddress()) {
            $html = $address->toString($address->getHtmlFormat());
        }
        else {
            $html = __('Customer doesn\'t have primary billing address');
        }
        return $html;
    }

    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }

    public function getSalesAccordionHtml()
    {
        return $this->getChildHtml('salesAccordion');
    }

}
