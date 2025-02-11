<?php

namespace App\Crud;

$db = DB::getInstance();

var_dump($params);

var_dump($db->get_foraneas($params['tablename']));
var_dump($db->get_schema($params['tablename']));
