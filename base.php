<?php
// +----------------------------------------------------------------------
// | markdown2mysql
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://saruri.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( MIT LICENSE )
// +----------------------------------------------------------------------
// | Author: saruri <sarurifan@gmail.com>
// +----------------------------------------------------------------------
namespace saruri;
include_once "datatransform.php";

/**
*# 号为 表名 然后接空格 注释表名
*例 # sys_think_php  系统基础表
*表头固定 三列
*>- 示范md
*> # sys_think_php  系统基础表
*> 字段|参数|备注
*> ---|---|---
*> id| int 11 key| 表id
*> name| varchar 255 notnull 不为空|姓名
*
*/
class MainService
{
    /**
     * @var array 配置参数
     */
    private  $_config = [
        'CHAR'=>1,
        'VARCHAR' => 100,
        'INT'=> 11,
        'FLOAT' => '(8,2)',
        'DOUBLE'=>'(8,2)',
        'TEXT'=>'',
        'TIMESTAMP'=>'',
        'DATETIME'=>'',
        'MEDIUMINT'=>6,
        'TINNYINT'=>1,
    ];

    private $_requestString;

    public $result; //返回的最终数组

    //初始
    public function __construct($string='')
    {
        $this->_config['requestString']=$string;
        //各种配置
        date_default_timezone_set('Asia/Shanghai');
        if($string==''){
            $this->_config['requestString']='*> # sys_think_php  系统基础表
            *> 字段|参数|备注
            *> ---|---|---
            *> id| int 11 key| 表id
            *> name| varchar 255 notnull 不为空|姓名';
        }
        //接收字符串
        self::init();
        //exit("shanghai");
    }
    
    //初始化参数
    public function init()
    {
        
        
        $arr=[];
        $md= new DataTransform($this->_config);
        $this->result=$md->run();
        //异常判断
        self::check();
    }

    //重载配置
    public function setConfig($config)
    {
        self::$_config=$config;
    }
    

    //执行
    public function run()
    {
       
        //获取数据
        $this->makeSql();

        //存储cookies
        self::saveCookies();
        //保存json
        self::saveJsonFile();
        
        //回传json
        $this->returnJson();
       

    }

    /* 
    *  @desc      获取数据
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 14:22:13  
    */ 
    public function makeSql()
    {   
        $sql="#DROP TABLE IF EXISTS ".$this->result['tableName'].";"."\n";
        $sql.="CREATE TABLE `".$this->result['tableName']."` (";
        $sql.=$this->joinField();
        $sql.=" ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT '".$this->result['tableComment']."';"."\n";
        $this->sql=$sql;
        
    }

    /* 
    *  @desc      拼接字段
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 23:14:54  
    */ 
    public function joinField()
    {
        $arr=$this->result['fields'];
        $string='';
        $endStr=end($arr);
        if($arr){
            foreach ($arr as $key => $result) {
                if($endStr==$result){
                    $string.= "`".$result['name']."` ".$result['type'].$result['length']." ".$result['key']." ".$result['auto']." ".$result['null']." ".$result['default']." COMMENT '".$result['comment']."'"."\n";
                }

                if($endStr!=$result){
                    $string.= "`".$result['name']."` ".$result['type'].$result['length']." ".$result['key']." ".$result['auto']." ".$result['null']." ".$result['default']." COMMENT '".$result['comment']."',"."\n";
                }
               
            }
        }

        return trim($string);

    }

    /* 
    *  @desc      返回数据
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 11:13:47  
    */ 
    public function returnJson()
    {
        //echo '返回了json'."\r\n";

        $arr= [
            'status' =>1,
            'msg'=>'sql数据',
            'data'=>$this->sql
        ];
        
        exit(json_encode($arr,true));


    }


    /* 
    *  @desc      方法说明
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 11:13:10  
    */ 
    public function saveCookies()
    {
        //echo '保存cookies'."\r\n";
    }

    /* 
    *  @desc      方法说明
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 11:12:35  
    */ 
    public function check()
    {
        //echo '异常判断'."\r\n";
    }



    /* 
    *  @desc      保存json
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 10:53:06  
    */ 
    public static function saveJsonFile()
    {
        //do something
        //echo '保存了json'."\r\n";
    }

   
}
$string='# 分销记录表 retaileRecord
项目 | 类型|说明表a 
---|---|---
retaile_record_id| int 8 auto key |收益记录id
parent_userid | int 8 |上级用户 
sub_userid|int 8 |下级用户
retaile_time| int 11|时间
retaile_type|tinyint 1|收益类型, 0 注册会员 ,1 认证企业,  2 认证个人,  3 vip1, 4 vip2
retaile_score1|整数 11 |收益金额
retaile_score2|日期 11 |收益金额
retaile_score3|时间戳 11 |收益金额
retaile_score4|时间 11 |收益金额
retaile_score5| 文本 |收益金额
retaile_score6|int 11 |收益金额';

//注释一下 kanances
$string=$_POST['md'];

$md= new MainService($string);
$md->run();
