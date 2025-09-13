<?php 
include $_SERVER['DOCUMENT_ROOT'].'/api/lib/wireGuard/wireguard.class.php';
include $_SERVER['DOCUMENT_ROOT'].'/api/lib/wireGuard/interface.class.php';

// // $wg = new Interfaces('wg0','10.0.0.1','9023');




$wg = new  Wireguard('wg0');
$wg->getPublickey();
// $wg->getUserData('dharfgdgani@gmail.com','0narZE9e6q9mHe3UNEF9opyNXCo/6p54W9yR+YbOchA=');

// function setConfiguration()
// {
//     $result  = $output = 0;
//     exec('whoami',$output);
//     print_r($output);
//     exec('cd wgctl && sudo ./main.py wg0 172.0.0.1 8783',$output,$return);
//     print($result);  
// }

// setConfiguration();
// class Test {
//     public static $result = false;

//     public function __destruct() {
//         // when object is destroyed, change static
//         self::$result = true;
//     }
// }

// echo "Before object: " . (Test::$result ? "true" : "false") . PHP_EOL;

// $obj = new Test();

// echo "After object created: " . (Test::$result ? "true" : "false") . PHP_EOL;

// // destroy explicitly
// unset($obj);

// echo "After unset: " . (Test::$result ? "true" : "false") . PHP_EOL;
// ?>
