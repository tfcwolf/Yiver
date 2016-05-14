<?php
namespace App\App;
use \Mysql;
use cms\Support\PasswordHash as PasswordHash;
use \Config;
/**
 * 登录验证
 * @copyright (c) Emlog All Rights Reserved
 */

class LoginAuth{

    const LOGIN_ERROR_USER = -1;
    const LOGIN_ERROR_PASSWD = -2;
    const LOGIN_ERROR_AUTHCODE = -3;


    public static function getTable()
    {
        return "{{user}}";
    }

    public static function getPassword()
    {
        return "password";
    }

    public static function getUserName()
    {
        return 'username';
    }
    /**
     * 验证用户是否处于登录状态
     */
    public static function isLogin() {
        global $userData;
        $auth_cookie = '';
        $cookie = \Config::get('cookie');
        if(isset($_COOKIE[$cookie])) {
            $auth_cookie = $_COOKIE[$cookie];
        } elseif (isset($_POST[$cookie])) {
            $auth_cookie = $_POST[$cookie];
        } else{
            return false;
        }
        if(($userData = self::validateAuthCookie($auth_cookie)) === false) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * 验证密码/用户
     *
     * @param string $username
     * @param string $password
     * @param string $imgcode
     * @param string $logincode
     */
    public static function checkUser($username, $password, $imgcode='', $logincode = false) {
        session_start();
        if (trim($username) == '' || trim($password) == '') {
            return false;
        } else {
            // $sessionCode = isset($_SESSION['code']) ? $_SESSION['code'] : '';
            // $logincode = false === $logincode ? Option::get('login_code') : $logincode;
            // if ($logincode == 'y' && (empty($imgcode) || $imgcode != $sessionCode)) {
            //     return self::LOGIN_ERROR_AUTHCODE;
            // }

            $userData = self::getUserDataByLogin($username);
            if (false === $userData) {
                return self::LOGIN_ERROR_USER;
            }
            $hash = $userData['password'];
            if (true === self::checkPassword($password, $hash)){
                return $userData;
            } else{
                return self::LOGIN_ERROR_PASSWD;
            }
        }
    }

    /**
     * 通过登录名查询管理员信息
     *
     * @param string $userLogin User's username
     * @return bool|object False on failure, User DB row object
     */
    public static function getUserDataByLogin($userLogin) {
        $DB = MySql::getInstance();
        if (empty($userLogin)) {
            return false;
        }
        $userData = false;

        $sql = "SELECT *,".self::getPassword()." as password FROM ".self::getTable()." WHERE ".self::getUserName()." = '$userLogin'";
     
        if (!$userData = $DB->once_fetch_array($sql)) {
            return false;
        }
        $userData['nickname'] = htmlspecialchars($userData['nickname']);
        $userData['username'] = htmlspecialchars($userData['username']);
        $userData['id'] = $userData['uid'];
        return $userData;
    }

    /**
     * 将明文密码和数据库加密后的密码进行验证
     *
     * @param string $password Plaintext user's password
     * @param string $hash Hash of the user's password to check against.
     * @return bool False, if the $password does not match the hashed password
     */
    public static function checkPassword($password, $hash) {
        global $em_hasher;
        if (empty($em_hasher)) {
            $em_hasher = new PasswordHash(8, true);
        }
        $check = $em_hasher->CheckPassword($password, $hash);
        return $check;
    }

    /**
     * 写用于登录验证cookie
     *
     * @param int $user_id User ID
     * @param bool $remember Whether to remember the user or not
     */
    public static function setAuthCookie($user_login,$username, $ispersis = false) {
        if ($ispersis) {
            $expiration  = time() + 60 * 60 * 24 * 30 * 12;
        } else {
            $expiration = null;
        }
        $auth_cookie_name = Config::get('cookie');
        $auth_cookie = self::generateAuthCookie($user_login,$username, $expiration);
        setcookie($auth_cookie_name, $auth_cookie, $expiration,'/');
    }

    /**
     * 生成登录验证cookie
     *
     * @param int $user_id user login
     * @param string  username 
     * @param int $expiration Cookie expiration in seconds
     * @return string Authentication cookie contents
     */
    private static function generateAuthCookie($user_login,$username, $expiration) {
        $key = self::emHash($user_login . '|' . $expiration);
        $hash = hash_hmac('md5', $user_login . '|' . $expiration, $key);

        $cookie = $user_login . '|' .$username.'|'. $expiration . '|' . $hash;

        return $cookie;
    }

    /**
     * Get hash of given string.
     *
     * @param string $data Plain text to hash
     * @return string Hash of $data
     */
    private static function emHash($data) {

        $key = Config::get('key');
        return hash_hmac('md5', $data, $key);
    }

    /**
     * 验证cookie
     * Validates authentication cookie.
     *
     * @param string $cookie Optional. If used, will validate contents instead of cookie's
     * @return bool|int False if invalid cookie, User ID if valid.
     */
    private static function validateAuthCookie($cookie = '') {
        if (empty($cookie)) {
            return false;
        }

        $cookie_elements = explode('|', $cookie);
        if (count($cookie_elements) != 4) {
            return false;
        }
        list($userid,$username, $expiration, $hmac) = $cookie_elements;

        if (!empty($expiration) && $expiration < time()) {
            return false;
        }

        $key = self::emHash($userid . '|' . $expiration);
        $hash = hash_hmac('md5', $userid . '|' . $expiration, $key);
        if ($hmac != $hash) {
            return false;
        }
        $user = self::getUserDataByLogin($username);
        if (!$user) {
            return false;
        }
        return $user;
    }
}