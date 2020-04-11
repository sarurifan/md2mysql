<?php
/**
 * 调用数据
 *
 * PHP version 7.2
 *
 * @category  INDEX
 * @package   SARURI
 * @author    saruri <saruri@163.com>
 * @copyright 2006-2019 saruri
 * @license   MIT LICENSE
 * @link      http://saruri.cn
 * @date      2019/12/28 19:04:42  
 */

namespace saruri;

include_once "base.php";
include_once "markdown2swagger.php";
//include_once "parser.php";


$_config = [
    'DB_HOST' => 'localhost',
    'DB_NAME'=> 'systemx_com',
    'DB_USER' => 'systemx_com',
    'DB_PWD' => 'mG3R6f7nk4J2dP2Z',   
];
$_config=[
    'DB_HOST' => 'localhost',
    'DB_NAME'=> 'thinkssns_com',
    'DB_USER' => 'thinkssns_com',
    'DB_PWD' => 'NHGMJBW7FfdF2etF', 
];
$m2m = new Mysql2Markdown($_config);




//示例 可选 更改css 
//$m2m ->changeCss('https://github.githubassets.com/assets/github-102d2679bcc893600ce928d5c6d34297.css');


if($_GET['action']=='json'){
    $m2m->toJson(); //示例输出成json
}


if($_GET['action']=='source'){
    $html=$m2m->toMarkdown();
}

if(!$_GET['action']){
    if($_GET['width']){
        //示例 可选 更改宽度 默认960
        $width=intval($_GET['width']);
        $width == 0 || $width==960;
        $m2m ->changeWidth($width);
    }
    //默认markdown
    $html=$m2m->toMarkdownParser();
}


exit($html);
