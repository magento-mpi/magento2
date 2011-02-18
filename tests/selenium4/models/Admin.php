<?php

/**
 * Admin framework model
 *
 * @author Magento Inc.
 */
class Model_Admin extends TestModelAbstract {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->baseUrl = Core::getEnvConfig('backend/baseUrl');
        $this->setBrowserUrl($this->baseUrl);
        $this->userName = Core::getEnvConfig('backend/auth/username');
        $this->password = Core::getEnvConfig('backend/auth/password');
    }

    /**
     * Performs login into the BackEnd
     *
     */
    public function doLogin($userName = null, $password = null)
    {
        $result = true;
        $userName = $userName ? $userName : $this->userName;
        $password = $password ? $password : $this->password;

        $this->open($this->baseUrl);
        $this->waitForPageToLoad("20000");

        $this->setUiNamespace('admin/pages/login');

        $this->type($this->getUiElement("fields/username"), $userName);
        $this->type($this->getUiElement("fields/password"), $password);
        $this->clickAndWait($this->getUiElement("buttons/loginbutton"));

        if ($this->isTextPresent($this->getUiElement("messages/invalidlogin"))) {
            $this->setVerificationErrors("Login check 1 failed: Invalid login name/passsword");
            $result = false;
        }
        if (!$this->waitForElement($this->getUiElement("images/mainlogo"), 30)) {
            $this->setVerificationErrors("Check 1 failed: Dashboard hasn't loaded");
            $result = false;
        }
        if ($result) {
            $this->printInfo('Logged to Admin');
        }
        return $result;
    }

    /**
     * Await appearing "Please wait" gif-image and disappearing
     *
     */
    public function pleaseWait()
    {
        $loadingMask = $this->getUiElement('/admin/elements/progress_bar');

        // await for appear and disappear "Please wait" animated gif...
        for ($second = 0;; $second++) {
            if ($second >= 60) {
                break; //fail("timeout");
            }
            try {
                if (!$this->isElementPresent($loadingMask)) {
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }

        for ($second = 0;; $second++) {
            if ($second >= 60

                )break;
            try {
                if ($this->isElementPresent($loadingMask)) {
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }
        sleep(1);
    }

    /**
     * Search row in the table satisfied condition Field "Key" == "value",
     * where "key" => "value" are parts of paramsArray
     * @param  $tableXPath
     * @param <type> $paramsArray
     * @return index of row, -1 if could not be founded
     * Note: this version works only for one(last) pair
     */
    public function getSpecificRow($tableXPath, $paramsArray)
    {
        $this->printDebug('getSpecificRow started');

        $colNum = $this->getXpathCount($tableXPath . "//tr[contains(@class,'heading')]//th");
        $this->printDebug('$colNum = ' . $colNum);
        $rowNum = $this->getXpathCount($tableXPath . "//tbody//tr");
        $this->printDebug('$rowNum = ' . $rowNum);

        foreach (array_keys($paramsArray) as $key) {
            //Open user with 'User Name' == name
            //Determine Column with 'User Name' title
            $keyColInd = -1;
            for ($col = 0; $col <= $colNum - 1; $col++) {
                $cellLocator = $tableXPath . '.0.' . $col;
                $cell = $this->getTable($cellLocator);
                if ($key == $cell) {
                    $this->printDebug($key . ' is founded in ' . $cellLocator);
                    $keyColInd = $col;
                }
                $this->printDebug($cellLocator . ' == ' . $cell);
            }
            if ($keyColInd == -1) {
                $this->printDebug($key . ' not founded in ' . $tableXPath . ' table');
                return -1;
            } else {
                if ($keyColInd > -1) {
                    $bodyLocator = $tableXPath . '//tbody';
                    $valueRowInd = -1;
                    for ($row = 0; $row <= $rowNum - 1; $row++) {
                        $cellLocator = $bodyLocator . '.' . $row . '.' . $keyColInd;
                        $cell = $this->getTable($cellLocator);
                        if ($cell == $paramsArray[$key]) {
                            $valueRowInd = $row;
                            $this->printDebug('Founded in ' . $cellLocator);
                        }
                        $this->printDebug($cellLocator . ' == [' . $cell . ']');
                    }
                }
            }
        }
        if ($valueRowInd > -1) {
            $valueRowInd++;
            return $valueRowInd;
        } else {
            $this->printDebug($paramsArray[$key] . ' not founded in ' . $tableXPath . ' table');
            return -1;
        }
    }

    /**
     * Verify is there a value for the field and select it.
     * It is necessary to set UiNamespace before using a function
     * @param <type> $field
     * @param <type> $value
     * $field path - "UiNamespace"/selectors/"$field"
     * @return boolean
     */
    public function checkAndSelectField($params, $field, $number)
    {
        $result = false;
        $Data = $params ? $params : $this->Data;
        if ($Data[$field] != NULL) {
            if ($this->isElementPresent($this->getUiElement("selectors/" . $field, $number) .
                            $this->getUiElement("/admin/elements/option_for_field", $Data[$field]))) {
                $this->select($this->getUiElement("selectors/" . $field, $number), "label=" . $Data[$field]);
                $result = true;
            } else {
                $this->printInfo("The value '" . $Data[$field] . "' cannot be set for the field '" . $field . "'");
            }
        } elseif ($Data[$field] == NULL) {
            $this->printInfo("The value for the field $field is not specified. The default value will be used");
        }
        return $result;
    }

    /**
     * Verify is there a value for the field and fill it
     * It is necessary to set UiNamespace before using a function
     * @param <type> $params
     * @param <type> $field
     * $field path - "UiNamespace"/inputs/"$field"     *
     * @param <type> $number
     * If $field is raised through Xpath expression such as //*[@id="selector_%s"] than %s=$number
     * It is necessary to set NULL if the $number is not needed
     * @return boolean
     */
    public function checkAndFillField($params, $field, $number)
    {
        $Data = $params ? $params : $this->Data;
        if (isset($Data[$field])) {
            $this->type($this->getUiElement("inputs/" . $field, $number), $Data[$field]);
        }
    }

    /**
     * Search element(s) and perform action on them.
     * It is necessary to set UiNamespace before using a function.
     * @param string $tableContainer
     * Принцип задания $tableContainer => XPath: UiNamespace/elements/$tableContainer
     * @param array $searchElements
     * ARRAY который содержит критерии по каким будет производится поиск и их значения.
     * Пример задания $searchElements: ar = array('searchBy1'=>'value1','searchBy2'=>'value2','searchBy2'=>'value2');,
     * где 'searchBy1', 'searchBy2', .. путь к XPath поля, для которого задается значение 'value1'.
     * Пока работает только с полями ввода. Принцип задания 'searchBy1' => XPath: UiNamespace/inputs/'searchBy1'
     * @param string $action
     * $action can have only 2 values: "mark"(mark the found element) and "open"(Open the found element)
     * @param <type> $tableNumber
     * If $tableContainer is raised through Xpath expression such as //*[@id="table_%s"] then %s=$tableNumber
     * When calling a function it is necessary to set NULL if the $tableNumber is not needed.
     * @return boolean
     */
    public function searchAndDoAction($tableContainer, $searchElements, $action, $tableNumber)
    {
        $result = TRUE;
        if (is_array($searchElements) and count($searchElements) > 0) {
            foreach ($searchElements as $key => $value) {
                $this->type($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                        $this->getUiElement("inputs/" . $key), $value);
            }
            $this->click($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                    $this->getUiElement("/admin/buttons/search"));
            if ($this->isTextPresent('Please wait...')) {
                $this->pleaseWait();
            } else {
                $this->waitForPageToLoad("30000");
            }
            if ($this->isTextPresent($this->getUiElement('/admin/elements/no_records'))) {
                $this->printInfo("\r\n No records found.");
                $result = FALSE;
            } elseif ($action == "mark") {
                $this->click($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                        $this->getUiElement('/admin/elements/mark_filtered_element', $searchElements));
            } elseif ($action == "open") {
                $this->click($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                        $this->getUiElement('/admin/elements/filtered_element', $searchElements));
                for ($second = 0;; $second++) {
                    if ($second >= 60)
                        break;
                    try {
                        if (!$this->isElementPresent($this->getUiElement("elements/" . $tableContainer, $tableNumber)))
                            break;
                    } catch (Exception $e) {

                    }
                    sleep(1);
                }
            }
        } else {
            $this->printInfo('Implementation of a function "searchAndDoAction" is skipped because data for the search is not specified ');
            $result = FALSE;
        }
        return $result;
    }

    /**
     * Click "Save" button and verify for errors.
     * 
     * @return boolean
     */
    function saveAndVerifyForErrors()
    {
        $result = false;
        $this->click($this->getUiElement("/admin/buttons/submit"));
        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/messages/error1'), 20)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/error1'));
            $this->setVerificationErrors($etext);
        } elseif (!$this->verifyPageAndGetErrors()) {
            // Check for success message
            if ($this->waitForElement($this->getUiElement('/admin/messages/success'), 60)) {
                $etext = $this->getText($this->getUiElement('/admin/messages/success'));
                $this->printInfo($etext);
                $result = true;
            } else {
                $this->setVerificationErrors('No success message');
            }
        }
        return $result;
    }

    /**
     * Проверяет есть ли на открытой странице поля с ошибками.Получает название ошибки и имя поля.
     * Работает для страниц которые содержат Табы и страницы создания ордера.
     *
     * @return boolean
     */
    public function verifyPageAndGetErrors()
    {
        $result = false;
        $this->setUiNamespace("admin/elements/");
        if ($this->isElementPresent($this->getUiElement("tabs_container") . $this->getUiElement("tab_error"))) {
            $qtyTab = $this->getXpathCount($this->getUiElement("tabs_container") . $this->getUiElement("tab_error"));
            $isTab = 1;
        } elseif ($this->isElementPresent($this->getUiElement("opened_tab") . $this->getUiElement("error_for_field"))) {
            $qtyTab = $this->getXpathCount($this->getUiElement("containers_for_order"));
            $isTab = 0;
        } else {
            $isTab = -1;
        }
        if ($isTab != -1) {
            $qtyErrors = $this->getXpathCount($this->getUiElement("error_for_field"));
            $this->printInfo("\r\n Found '" . $qtyErrors . "' error(s)");
            for ($y = 1; $y <= $qtyTab; $y++) {
                switch ($isTab) {
                    case 1:
                        $tabName = $this->getText($this->getUiElement("tabs_container") . $this->getUiElement("tab_error_many", $y));
                        $this->click($this->getUiElement("tabs_container") . $this->getUiElement("tab_error_many", $y));
                        $this->printInfo("'" . $tabName . "' tab contains invalid data:");
                        $errorXpath = $this->getUiElement("opened_tab") . $this->getUiElement("error_for_field");
                        break;
                    case 0:
                        $errorXpath = $this->getUiElement("containers_for_order") . "[$y]" . $this->getUiElement("error_for_field");
                        break;
                }
                $qtyFields = $this->getXpathCount($errorXpath);
                for ($i = 1; $i <= $qtyFields; $i++) {
                    if ($this->isElementPresent($errorXpath . "[$i]" . $this->getUiElement("field_name_with_error"))) {
                        $fieldName = $this->getText($errorXpath . "[$i]" . $this->getUiElement("field_name_with_error"));
                    } else {
                        $fieldName = $this->getAttribute($errorXpath . "[$i]" . "@id");
                        $fieldName = strrev($fieldName);
                        $fieldName = strrev(substr($fieldName, 0, strpos($fieldName, "-")));
                    }
                    $errorName = $this->getText($errorXpath . "[$i]");
                    $this->printInfo("\r\n Field '" . $fieldName . "' contains error - '" . $errorName . "'");
                }
            }
            $result = true;
        }
        return $result;
    }

}

