<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Connect;

/**
 * Class to validate string resources
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Validator
{
    /**
     * @var string[]
     */
    protected static $_stability = array(0 => 'devel', 1 => 'alpha', 2 => 'beta', 3 => 'stable');

    /**
     * @return string[]
     */
    public static function getStabilities()
    {
        return self::$_stability;
    }

    /**
     * Compare stabilities.
     *
     * Returns:
     * -1 if the first stability is lower than the second
     *  0 if they are equal
     *  1 if the second is lower.
     *
     * @param int $s1
     * @param int $s2
     * @return int|void
     * @throws \Exception
     */
    public function compareStabilities($s1, $s2)
    {
        $list = $this->getStabilities();
        $tmp = array_combine(array_values($list), array_keys($list));

        if (!isset($tmp[$s1], $tmp[$s2])) {
            throw new \Exception("Invalid stability in compareStabilities argument");
        }

        // 'stable' turns to 3
        // 'devel' turns to 0
        $s1 = $tmp[$s1];
        $s2 = $tmp[$s2];
        if ($s1 === $s2) {
            return 0;
        } elseif ($s1 > $s2) {
            return 1;
        } elseif ($s1 < $s2) {
            return -1;
        }
    }

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Validate max len of string
     * @param string $str
     * @param int $maxLen
     * @return bool
     */
    public function validateMaxLen($str, $maxLen)
    {
        return strlen((string)$str) <= (int)$maxLen;
    }

    /**
     * Validate channel name and url
     *
     * @param string $str
     * @return bool
     */
    public function validateChannelNameOrUri($str)
    {
        return $this->validateUrl($str) || $this->validatePackageName($str);
    }

    /**
     * Validate License url
     *
     * @param string $str
     * @return boolean
     */
    public function validateLicenseUrl($str)
    {
        if ($str) {
            return $this->validateUrl($str) || $this->validatePackageName($str);
        }
        return true;
    }

    /**
     * Validate compatible data
     * @param array $data
     * @return bool
     */
    public function validateCompatible(array $data)
    {
        if (!count($data)) {
            /**
             * Allow empty
             */
            return true;
        }
        $count = 0;
        foreach ($data as $k => $v) {
            foreach (array('name', 'channel', 'min', 'max') as $fld) {
                ${$fld} = trim($v[$fld]);
            }
            $count++;

            $res = $this->validateUrl($channel) && strlen($channel);
            if (!$res) {
                $this->addError("Invalid or empty channel in compat. #{$count}");
            }

            $res = $this->validatePackageName($name) && strlen($name);
            if (!$res) {
                $this->addError("Invalid or empty name in compat. #{$count}");
            }
            $res1 = $this->validateVersion($min);
            if (!$res1) {
                $this->addError("Invalid or empty minVersion in compat. #{$count}");
            }
            $res2 = $this->validateVersion($max);
            if (!$res2) {
                $this->addError("Invalid or empty maxVersion in compat. #{$count}");
            }
            if ($res1 && $res2 && $this->versionLower($max, $min)) {
                $this->addError("Max version is lower than min in compat #{$count}");
            }
        }
        return !$this->hasErrors();
    }

    /**
     * Validate authors of package
     * @param array $authors
     * @return bool
     */
    public function validateAuthors(array $authors)
    {
        if (!count($authors)) {
            $this->addError('Empty authors section');
            return false;
        }
        $count = 0;
        foreach ($authors as $k => $v) {
            $count++;
            array_map('trim', $v);
            $name = $v['name'];
            $login = $v['user'];
            $email = $v['email'];
            $res = $this->validateMaxLen($name, 256) && strlen($name);
            if (!$res) {
                $this->addError("Invalid or empty name for author #{$count}");
            }
            $res = $this->validatePackageName($login) && strlen($login);
            if (!$res) {
                $this->addError("Invalid or empty login for author #{$count}");
            }
            $res = $this->validateEmail($email);
            if (!$res) {
                $this->addError("Invalid or empty email for author #{$count}");
            }
        }
        return !$this->hasErrors();
    }

    /**
     * Validator errors
     * @var array
     */
    private $_errors = array();

    /**
     * Add error
     * @param string $err
     * @return void
     */
    private function addError($err)
    {
        $this->_errors[] = $err;
    }

    /**
     * Set validator errors
     * @param array $err
     * @return void
     */
    private function setErrors(array $err)
    {
        $this->_errors = $err;
    }

    /**
     * Clear validator errors
     * @return void
     */
    private function clearErrors()
    {
        $this->_errors = array();
    }

    /**
     * Check if there are validator errors set
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->_errors) != 0;
    }

    /**
     * Get errors
     * @param bool $clear if true after this call erros will be cleared
     * @return array
     */
    public function getErrors($clear = true)
    {
        $out = $this->_errors;
        if ($clear) {
            $this->clearErrors();
        }
        return $out;
    }

    /**
     * Validate URL
     * @param string $str
     * @return bool
     */
    public function validateUrl($str)
    {
        $regex = "@([0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}|" .
            "(((news|telnet|nttp|file|http|ftp|https)://)|(www|ftp)" .
            "[-A-Za-z0-9]*\\.)[-A-Za-z0-9\\.]+)(:[0-9]*)?@i";
        return preg_match($regex, $str);
    }

    /**
     * Validates package stability
     * @param string $str
     * @return bool
     */
    public function validateStability($str)
    {
        return in_array(strval($str), self::$_stability);
    }

    /**
     * Validate date format
     * @param string $date
     * @return bool
     */
    public function validateDate($date)
    {
        $subs = null;
        $check1 = preg_match("/^([\d]{4})-([\d]{2})-([\d]{2})$/i", $date, $subs);
        if (!$check1) {
            return false;
        }
        return checkdate($subs[2], $subs[3], $subs[1]);
    }

    /**
     * Validate email
     * @param string $email
     * @return bool
     */
    public function validateEmail($email)
    {
        return preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email);
    }

    /**
     * Validate package name
     * @param string $name
     * @return bool
     */
    public function validatePackageName($name)
    {
        return preg_match("/^[a-zA-Z0-9_-]+$/i", $name);
    }

    /**
     * Validate version number
     * @param string $version
     * @return bool
     */
    public function validateVersion($version)
    {
        return preg_match("/^[\d]+\.[\d]+\.[\d]+([[:alnum:]\.\-\_]+)?$/i", $version);
    }

    /**
     * Check versions are equal
     * @param string $v1
     * @param string $v2
     * @return bool
     */
    public function versionEqual($v1, $v2)
    {
        return version_compare($v1, $v2, "==");
    }

    /**
     * Check version $v1 <= $v2
     * @param string $v1
     * @param string $v2
     * @return bool
     */
    public function versionLowerEqual($v1, $v2)
    {
        return version_compare($v1, $v2, "le");
    }

    /**
     * Check if version $v1 lower than $v2
     * @param string $v1
     * @param string $v2
     * @return bool
     */
    public function versionLower($v1, $v2)
    {
        return version_compare($v1, $v2, "<");
    }

    /**
     * Check version $v1 >= $v2
     * @param string $v1
     * @param string $v2
     * @return bool
     */
    public function versionGreaterEqual($v1, $v2)
    {
        return version_compare($v1, $v2, "ge");
    }

    /**
     * Generic regex validation
     * @param string $str
     * @param string $regex
     * @return bool
     */
    public function validateRegex($str, $regex)
    {
        return preg_match($regex, $str);
    }

    /**
     * Check if PHP extension loaded
     * @param string $name Extension name
     * @return bool
     */
    public function validatePhpExtension($name)
    {
        return extension_loaded($name);
    }

    /**
     * @param string|bool $min
     * @param string|bool $max
     * @param string $ver
     * @return bool
     */
    public function validatePHPVersion($min, $max, $ver = PHP_VERSION)
    {
        $minAccepted = true;
        if ($min) {
            $minAccepted = version_compare($ver, $min, ">=");
        }
        $maxAccepted = true;
        if ($max) {
            $maxAccepted = version_compate($ver, $max, "<=");
        }
        return (bool)$minAccepted && $maxAccepted;
    }
}
