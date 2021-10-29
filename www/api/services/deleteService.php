<?php

$post = file_get_contents('php://input');
$data = json_decode($post, true);

if(isset($data['token']) && SystemMethods::checkToken($data['token'])){

    if(isset($data['id'])){
        $sth = $dbh->prepare('DELETE FROM `services` WHERE id = :id');
        $sth->execute(['id' => $data['id']]);
    }

    return $data['token'];
}else{
    http_response_code(403);
}