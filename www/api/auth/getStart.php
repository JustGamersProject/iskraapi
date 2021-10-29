<?php

$service_data = $dbh->query('SELECT id, name, image, header, text FROM `services`')->fetchAll();
$cases_data = $dbh->query('SELECT id, name, image, url, header, text FROM `cases`')->fetchAll();

return array(
    'service_data' => $service_data,
    'cases_data' => $cases_data
);