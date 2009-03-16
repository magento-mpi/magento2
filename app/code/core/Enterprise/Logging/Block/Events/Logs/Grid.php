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
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Logging_Block_Events_Logs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        $this->setSaveParametersInSession(true);
        $this->setId('logsGrid');
        $this->setDefaultSort('filename', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * Init logs collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getSingleton('enterprise_logging/logs_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $url7zip = Mage::helper('adminhtml')->__('The archive can be uncompressed with <a href="%s">%s</a> on Windows systems', 'http://www.7-zip.org/', '7-Zip');

        $gridUrl = $this->getUrl('*/*/');

        $this->addColumn('download', array(
            'header'    => Mage::helper('backup')->__('Download'),
            'format'    => '<a href="' . $gridUrl .'download/?name=$filename">$filename</a>',
            'index'     => 'filename',
            //'filter'    => false
        ));

        return $this;
    }

    /**
     * return grid url
     */
    public function getGridUrl() 
    {
         return $this->getUrl('adminhtml/events/loggrid', array('_current'=>true));
    }

}
