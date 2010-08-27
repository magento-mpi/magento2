<?php
/**
 * Abstract test class for Frontend module
 *
 * @author Magento Inc.
 */
abstract class Test_Frontend_Abstract extends Test_Abstract
{
    /**
     * Helper local instance
     *
     * @var Helper_Admin
     */
    protected $_helper = null;

    /**
     * Initialize the environment
     */
    public function  setUp() {
        parent::setUp();

        // Get test parameters
        $this->_baseurl = Core::getEnvConfig('frontend/baseUrl');
        $this->_email = Core::getEnvConfig('frontend/auth/email');
        $this->_password = Core::getEnvConfig('frontend/auth/password');
    }

    /**
     * Login to the FrontEnd
     *
     */
    public function frontLogin($email, $password) {
        $this->open($this->_baseurl);
        $this->clickAndWait($this->getUiElement("frontend/pages/home/links/myAccount"));
    }

    /**
     * Register customer from FrontEnd
     *
     */
    public function frontRegister($params) {
        $this->open($this->_baseurl);
        $this->clickAndWait($this->getUiElement("frontend/pages/home/links/myAccount"));
        $this->clickAndWait($this->getUiElement("frontend/pages/login/buttons/register"));
        // Fill register information
        $this->type($this->getUiElement("frontend/pages/register/inputs/firstName"),$params["firstName"]);
        $this->type($this->getUiElement("frontend/pages/register/inputs/lastName"),$params["lastName"]);
        $this->type($this->getUiElement("frontend/pages/register/inputs/email"),$params["email"]);
        $this->type($this->getUiElement("frontend/pages/register/inputs/password"),$params["password"]);
        $this->type($this->getUiElement("frontend/pages/register/inputs/confirmation"),$params["password"]);
        //Register customer
        $this->clickAndWait($this->getUiElement("frontend/pages/register/buttons/submit"));
        //Check for some specific validation errors:
        if ($this->isTextPresent($this->getUiElement("frontend/pages/register/messages/alreadyExists"))) {
                $this->setVerificationErrors("frontRegister check 1: customer with such email already registered");
        } else {
            // Check for success message
            if (!$this->waitForElement($this->getUiElement("frontend/pages/register/messages/customerRegistered"),2)) {
                $this->setVerificationErrors("frontRegister check 1: no success message");
            }
        }
    }

    /**
     * Cpen $categoryName category page.
     * @param $categoryName
     * @return boolean
     */
    public function doOpenCategory($categoryName)
    {
        if ($this->waitForElement($this->getUiElement("frontend/pages/category/links/subCategory",$categoryName),5)) {
            //Move to Category
            $this->clickAndWait($this->getUiElement("frontend/pages/category/links/subCategory",$categoryName));
        } else {
                Core::debug('doOpenCategory: "' . $categoryName . '" category page could not be opened', 5);
                return false;
        }
        Core::debug('doOpenCategory: "' . $categoryName . '" category page has been opened', 7);
        return true;
    }

    /**
     * Cpen $categoryName category page, find $productName link and open $productName page
     * @param $categoryName
     * @param $productName
     * @return boolean
     */
    public function doOpenProduct($categoryName, $productName)
    {
        if ($this->doOpenCategory($categoryName)) {
            if ($this->waitForElement($this->getUiElement("frontend/pages/category/links/productName",$productName),5)) {
                //Move to Category
                $this->clickAndWait($this->getUiElement("frontend/pages/category/links/productName",$productName));
            } else {
                    Core::debug('doOpenProduct: "' . $productName . '" product page could not be opened', 5);
                    return false;
            }
            Core::debug('doOpenProduct: "' . $productName . '" product page has been opened', 7);
            return true;
        }
    }

    /*
    That it is an implementation of the function money_format for the
    platforms that do not it bear.

    The function accepts to same string of format accepts for the
    original function of the PHP.

    (Sorry. my writing in English is very bad)

    The function is tested using PHP 5.1.4 in Windows XP
    and Apache WebServer.
    */
    function money_format($format, $number)
    {
        $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'.
                  '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
                               $match[1] : ' ',
                'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                               $match[0] : '+',
                'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value  *= -1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                            ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                            $csuffix;
            } else {
                $currency = '';
            }
            $space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format($value, $right, $locale['mon_decimal_point'],
                     $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                         STR_PAD_RIGHT : STR_PAD_LEFT);
            }

            $format = str_replace($fmatch[0], $value, $format);
        }
        return $format;
    }
}

