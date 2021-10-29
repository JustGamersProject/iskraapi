<?php

$post = file_get_contents('php://input');
$data = json_decode($post, true);

if(isset($data['token']) && SystemMethods::checkToken($data['token'])){

    //подготовка данных для запроса
    $insert_data = array();
    $prepare_data = array();

    if(isset($data['img'])){
        $insert_data[] = 'image = :image'; 
        $prepare_data['image'] = $data['img'];
    }

    if(isset($data['service_name'])){
        $insert_data[] = 'name = :name';
        $prepare_data['name'] = $data['service_name'];
    }

    if(isset($data['header'])){
        $insert_data[] = 'header = :header';
        $prepare_data['header'] = $data['header'];
    }

    if(isset($data['text'])){
        $insert_data[] = 'text = :text';
        $prepare_data['text'] = $data['text'];
    }

    if(count($insert_data) > 0){
        $set = implode(',', $insert_data);
        $sth = $dbh->prepare('INSERT INTO `services` SET '. $set .'');
        $sth->execute($prepare_data);
    }

    return $data['token'];
}else{
    http_response_code(403);
}