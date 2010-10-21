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
 * @package     Mage_DirectPayment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Direct Payment Data Helper
 *
 * @category   Mage
 * @package    Mage_DirectPayment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_DirectPayment_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieve save order url
     *    
     * @return  string
     */
    public function getSaveOrderUrl()
    {
        switch ($this->getControllerName()) {
            case 'onepage':
                $route = 'checkout/onepage/saveOrder';
                break;
            case 'multishipping':
                $route = 'directpayment/multishipping';
                break;
            case 'sales_order_create':
            case 'sales_order_edit':
                $route = '*/'.$this->getControllerName().'/save';
                break;
            default:
                $route = 'checkout/onepage/saveOrder';
                break;
        }
        
        return $this->_getUrl($route);
    }
    
    /**
     * Retrieve place order url
     *    
     * @return  string
     */
    public function getPlaceOrderUrl()
    {
        return $this->_getUrl('directpayment/paygate/place');
    }
    
	/**
     * Retrieve place order url
     * 
     * @param array params  
     * @return  string
     */
    public function getSuccessOrderUrl($params)
    {
        $param = array();
        switch ($params['controller_action_name']) {
            case 'onepage':
                $route = 'checkout/onepage/success';
                break;
            case 'multishipping':
                $route = 'checkout/multishipping/success';                
                break;
            case 'sales_order_create':
            case 'sales_order_edit':
                $route = '*/sales_order/view';
                if (isset($params['x_invoice_num'])) {
                    $order = Mage::getModel('sales/order')->loadByIncrementId($params['x_invoice_num']);
                    $param['order_id'] = $order->getId();
                }
                break;
            default:
                $route = 'checkout/onepage/success';
                break;
        }
        
        return $this->_getUrl($route, $param);
    }
    
    /**
     * Get controller name
     * 
     * @return string
     */
    public function getControllerName()
    {
        return Mage::app()->getFrontController()
                            ->getRequest()
                            ->getControllerName();
    }
    
    /**
     * Wrap js code for iframe
     * 
     * @param mixed $jsCode
     * @return string
     */
    public function wrapHtml($jsCode)
    {
        return '<html>
            		<head>
            			<script type="text/javascript">
            			//<![CDATA[
            			'.$jsCode.'
            			//]]>
            			</script>
            		</head>
            		<body></body>
        	    </html>';
    }
    
    /**
     * Get iframe html
     * 
     * @param array $params
     * @return string
     */
    public function getIframeHtml($params)
    {
        if (isset($params['x_response_code'])) {            
            $jS = '';
            if ($params['x_response_code'] == 1 &&
                isset($params['x_invoice_num'])) {
               $jS .= 'window.top.location="'.$this->getSuccessOrderUrl($params).'"';                
            }            
            else {
                $jS .= 'if (window.top.review) {
                		    window.top.review.resetLoadWaiting();
                		}                		
                		window.top.directPaymentModel.showError("'.$params['x_response_reason_text'].'");';
                if (isset($params['x_invoice_num'])) {
                    $jS .= 'window.top.directPaymentModel.successUrl='.$this->getSuccessOrderUrl($params['x_invoice_num']);
                }                          
            }
            
            return $this->wrapHtml($jS);
        }        
    }
}