<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminUser
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class testNAME extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /*
     *  1. create order
     *  2. work with calendar
     *  2.1 get current data PHP (+)
     *  2.2 convert it in format MM/DD/YYYY (+)
     *  3. find records in grid
     *  4. get count of rows in grid in different conditions
     *
     *
     *
     *
     *
     *
     *
     */





    public function test()
    {
        $date = getdate();
        $data2= date('m/d/y');
        $validDate = $date['mon'] . '/' . $date['mday']. '/' . $date['year'] ;

        $this->navigate('report_customer_totals');
        $this->fillField('filter_from',$validDate);
        $this->fillField('filter_to',$validDate);
        $this->clickButton('refresh');

        $gridXpath = "//table[@id='gridTotalsCustomer_table']";



        echo $validDate;




        //echo sd;

    }

    /**
     * @param $gridXpath
     * @param $numbTestColumn
     * @param $linkForSortColumnType
     *
     * @return array
     */
    public function getAllColumnValues($gridXpath, $numbTestColumn, $linkForSortColumnType=null)
    {
        $xpathOfAllRecordsPerPage = $gridXpath . '/tbody/tr';

        $resultArray = array();
        if($this->isElementPresent($xpathOfAllRecordsPerPage . '[1]/td[' . $numbTestColumn .']'))
        {
            $count = $this->pagesCount();
            $currentPage = 0;
            do {
                $currentPage++;
                $this->_paginate($currentPage);
                $resultArray = array_merge($this->collectArray($gridXpath, $numbTestColumn),$resultArray);
            } while ($count != $currentPage);
        }

        return $resultArray;
    }

    /**
     * Go to specified page
     *
     * @param $currentPage
     */
    protected function _paginate($currentPage)
    {
        $this->fillField('page', $currentPage);
        $this->keyPress($this->_getControlXpath('field', 'page'), "\\13");
        $this->waitForAjax();
    }

    /**
     * @param $gridXpath
     * @param $numbTestColumn
     *
     * @return array
     */
    public function collectArray($gridXpath, $numbTestColumn)
    {
        $allColumnArrayFromPage = array();
        $count = count($this->getElementsByXpath($gridXpath . '/tbody/tr'));
        for ($i=1; $i <= $count; $i++)
        {
            $allColumnArrayFromPage[] =
                $this->getElementByXpath($gridXpath . '/tbody/tr[' . $i. ']/td[' . $numbTestColumn . ']');
        }

        return $allColumnArrayFromPage;

    }


    public function pagesCount()
    {
        $page = $actualPage = 0;
        do {
            $page++;
            $this->_paginate($page);
            $actualPage = $this->getValue($this->_getControlXpath('field', 'page'));
        } while ($page == $actualPage);

        return $actualPage;
    }



}