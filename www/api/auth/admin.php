<?php

$post = file_get_contents('php://input');
$data = json_decode($post, true);

if(isset($data['admin'], $data['password'])){

    //по логину находим данные пользователя
    $sth = $dbh->prepare('SELECT id, user_password, user_token, datetime FROM `auth` WHERE user_login = :user_login');
    $sth->execute(['user_login' => $data['admin']]);
    $user_data = $sth->fetch();

    if(isset($user_data) && password_verify($data['password'], $user_data['user_password'])){

        $datetime1 = date_create($user_data['datetime']);
        $datetime2 = date_create('now');
        $datediff = date_diff($datetime1, $datetime2)->days;

        // если токена нету или действие токена истекло (не более двух дней) продлеваем его 
        if(!isset($user_data['user_token']) || $datediff > 2){ 
            $sth = $dbh->prepare('UPDATE `auth` SET user_token = :user_token, datetime = :datetime WHERE id = :id');
            $sth->execute([
                'user_token' => SystemMethods::guidv4(),
                'datetime' => date('Y-m-d H:i:s', strtotime('+2 days')),
                'id' => $user_data['id']
            ]);
        }

        // возвращаем токен
        $sth = $dbh->prepare('SELECT user_token FROM `auth` WHERE id = :id');
        $sth->execute(['id' => $user_data['id']]);
        return $sth->fetch()['user_token'];
    }else{
        http_response_code(401);
    }
}else{
    http_response_code(401);

}