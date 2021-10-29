<?php

$post = file_get_contents('php://input');
$data = json_decode($post, true);

if(isset($data['token']) && SystemMethods::checkToken($data['token'])){

    //подготовка данных для запроса
    $update_data = array();
    $prepare_data = array();

    if(isset($data['img'])){
        $update_data[] = 'image = :image'; 
        $prepare_data['image'] = $data['img'];
    }

    if(isset($data['service_name'])){
        $update_data[] = 'name = :name';
        $prepare_data['name'] = $data['service_name'];
    }

    if(isset($data['header'])){
        $update_data[] = 'header = :header';
        $prepare_data['header'] = $data['header'];
    }

    if(isset($data['text'])){
        $update_data[] = 'text = :text';
        $prepare_data['text'] = $data['text'];
    }

    if(count($update_data) > 0 && isset($data['id'])){

        $set = implode(',', $update_data);
        $prepare_data['id'] = $data['id'];

        $sth = $dbh->prepare('UPDATE `services` SET '. $set .' WHERE id = :id');
        $sth->execute($prepare_data);
    }

    return $data['token'];
}else{
    http_response_code(403);
}

