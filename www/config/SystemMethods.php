<?php

class SystemMethods
{
    public static function guidv4()
    {
        $data = random_bytes(16) ?? openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // выводит 36-символьный UUID
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function addError($error)
    {
        global $errors;
        $errors[] = $error;
    }

    public static function checkToken($token)
    {
        global $dbh;
        $sth = $dbh->prepare('SELECT id FROM `auth` WHERE user_token = :user_token');
        $sth->execute(['user_token' => $token]);
        return $sth->fetch()['id'];
    }
}
