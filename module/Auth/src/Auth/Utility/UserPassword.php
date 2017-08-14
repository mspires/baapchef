<?php

namespace Auth\Utility;

use Zend\Crypt\Password\Bcrypt;

class UserPassword
{

    public $salt = 'aUJGgadjasdgdj';

    public $method = 'sha1';

    public function __construct($method = null)
    {
        if (! is_null($method)) {
            $this->method = $method;
        }
    }

    public function create($password)
    {
        if ($this->method == 'md5') {
            return md5($this->salt . $password);
        } elseif ($this->method == 'sha1') {
            return sha1($this->salt . $password);
        } elseif ($this->method == 'bcrypt') {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);
            return $bcrypt->create($password);
        } elseif ($this->method == 'plain') {
            return $password;
        }
    }

    public function verify($password, $hash)
    {
        if ($this->method == 'md5') {
            return $hash == md5($this->salt . $password);
        } elseif ($this->method == 'sha1') {
            return $hash == sha1($this->salt . $password);
        } elseif ($this->method == 'bcrypt') {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);
            return $bcrypt->verify($password, $hash);
        }
    }

    public function generate($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        //return substr(str_shuffle($chars),0,$length);
		return "12345678";
    }
    
    public function generatePIN($length)
    {
        $chars = "0123456789";
        //return substr(str_shuffle($chars),0,$length);
		return "1234";       
    }
}
