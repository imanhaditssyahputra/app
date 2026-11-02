<?php

// $session = CodeIgniter\Config\BaseService::session();

namespace App\Config\Session;

function __construct()
{
    $model_Global =  model('Global_model','M_GLOBAL');
    $session = session();
    $db = db_connect();
    
}
