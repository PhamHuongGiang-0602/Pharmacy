<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
ob_start();

require_once('config.php');
require_once('config/constants.php');
require_once 'config/database.php';

require_once 'app/models/BaseModel.php';
require_once 'app/views/layout/header.php';
require_once 'app/views/home.php'; 
require_once 'app/views/layout/footer.php';

