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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Staging Manage Grid
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('enterpriseStagingManageGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

        $this->setColumnRenderers(
            array(
                'action' => 'enterprise_staging/manage_staging_renderer_action'
            ));
    }

    /**
     * PrepareCollection method.
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('enterprise_staging/staging_collection');

        foreach($collection AS $staging) {
            $collection->getItemById($staging->getId())
                ->setData("lastEvent", $staging->getEventsCollection()->getFirstItem()->getComment());
            $defaultStore = $staging->getStagingWebsite()->getDefaultStore();
            if ($defaultStore) {
                if ($defaultStore->isFrontUrlSecure()) {
                    $baseUrl = $defaultStore->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true);
                } else {
                    $baseUrl = $defaultStore->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
                }
            } else {
                $baseUrl = '';
            }

            $collection->getItemById($staging->getId())
                ->setData("base_url", $baseUrl);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => 'Name',
            'index'     => 'name',
            'type'      => 'text',
        ));

        $this->addColumn('base_url', array(
            'width'     => '250px',
            'header'    => 'Url',
            'index'     => 'base_url',
            'title'     => 'base_url',
            'length'    => '40',
            'type'      => 'action',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('lastEvent', array(
            'width'     => '250px',
            'header'    => 'Latest Event',
            'index'     => 'lastEvent',
            'type'      => 'text',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('created_at', array(
            'width'     => '100px',
            'header'    => 'Created At',
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('updated_at', array(
            'width'     => '100px',
            'header'    => 'Updated At',
            'index'     => 'updated_at',
            'type'      => 'datetime',
        ));

        return $this;
    }

    /**
     * Return grids url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Return Row Url
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId())
        );
    }
}