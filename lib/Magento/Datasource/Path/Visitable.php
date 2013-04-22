<?php
/**
 * Data source visitable interface
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Datasource_Path_Visitable
{
    public function visit(Magento_Datasource_Path_Visitor $visitor);
}