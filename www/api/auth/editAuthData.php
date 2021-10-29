<?php

$post = file_get_contents('php://input');
$data = json_decode($post, true);

if(isset($data['token']) && SystemMethods::checkToken($data['token'])){

    //по токену находим данные пользователя
    $sth = $dbh->prepare('SELECT id, user_login, user_password FROM `auth` WHERE user_token = :user_token');
    $sth->execute(['user_token' => $data['token']]);
    $auth_data = $sth->fetch();

    if(isset($auth_data)){

        //подготовка данных для запроса
        $user_data = array();
        $prepare_data = array();

        if(isset($data['admin']) && $data['admin'] != ''){
            $user_data[] = 'user_login = :user_login'; 
            $prepare_data['user_login'] = $data['admin'];
        }

        if(isset($data['password']) && $data['password'] != ''){
            $user_data[] = 'user_password = :user_password';
            $prepare_data['user_password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if(count($user_data) > 0){

            $set = implode(',', $user_data);
            $prepare_data['id'] = $auth_data['id'];

            $sth = $dbh->prepare('UPDATE `auth` SET '. $set .' WHERE id = :id');
            $sth->execute($prepare_data);
        }

        return $data['token'];
    }else{
        http_response_code(403);
    }
}else{
    http_response_code(403);
}