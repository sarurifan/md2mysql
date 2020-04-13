<?php

/**
 * 转换数据
 *
 * PHP version 7.2
 *
 * @category  API
 * @package   YII
 * @author    saruri <saruri@163.com>
 * @copyright 2006-2019 saruri
 * @license   https://saruri.cn/licence.txt BSD Licence
 * @link      http://saruri.cn
 * @date      2020/04/12 11:28:29  
 */
namespace saruri;
//use Yii;
class DataTransform
{
   private $_config = [];
   //初始化
   public function __construct($_config)
   {
        //各种配置
        $this->_config=$_config;
        $this->init();
   }
      
   //继承上级的初始化
   public function init()
   {
        //可选
        $this->_config['requestString']=str_replace("#","",$this->_config['requestString']);
        $this->_config['requestString']=str_replace(">","",$this->_config['requestString']);
        $this->_config['requestString']=str_replace("*","",$this->_config['requestString']);
        $this->arrResult=explode("\r\n",$this->_config['requestString']);
        
   }
      
    /* 
    *  @desc      保存字段数据
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 11:10:21  
    */ 
    public function saveTableFiled()
    {
        $arr=$this->arrResult;
        unset($arr[0]);
        unset($arr[1]);
        unset($arr[2]);
        //var_dump($arr);
        //echo '保存字段'."\r\n";
        foreach ($arr as $key => $value) {
            # code...
            $filed=$this->getField($value);
            $this->tableConfig['fields'][$filed['name']]=$filed;
        }


    }

    /* 
    *  @desc      获取具体字段数据
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 17:12:01  
    */ 
    public function getField($string)
    {
        //do something
        $string=trim($string);
        $arr=explode("|",$string);
        $result['name']=trim($arr[0]);//字段名称
        $result['comment']=$arr[2];//字段中文说明
        $result['type']=$this->getType($string);//类型
        $result['length']=$this->getLength($string);//长度
        $result['key']=$this->getKey($string);//主键
        $result['null']=$this->getNull($string);//是否允许空
        $result['auto']=$this->getAuto($string);//是否自增
        $result['default']=$this->getDefault($string);//是否自增
        return $result;
    }

    /* 
    *  @desc      获取类型 就设置了几个简单类型 多的您可以自行扩展
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 19:57:25  
    */ 
    public function getType($string)
    {
        $arr=[
            'tinyint'=>'tinyint',
            'mediumint'=>'mediumint',
            'int'=>'int',
            'float'=>'float',
            'double'=>'double',
            'varchar'=>'varchar',
            'char'=>'char',
            'text'=>'text',
            '整数'=>'int',
            '文本'=>'text',
            'datetime'=>'datetime',
            '时间戳'=>'timestamp',
            '时间'=>'time',
            '日期'=>'datetime',

        ];
        //数字类
        //return $string;
        foreach ($arr as $key => $value) {
            if($this->check_str($string,$key)){  return strtoupper($value);  }
        }
        
        return 'VARCHAR';

    }

    public function check_str($str, $substr)
    {
     $nums=substr_count($str,$substr);
     if ($nums>=1)
     {
      return true;
     }
     else
     {
      return false;
     }
    }


    /* 
    *  @desc      获取长度
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 19:57:25  
    */ 
    public function getLength($string)
    {
        //ALERT TABLE table-name ADD col-name col-type COMMENT 'xxx';
        $arr=explode("|",trim($string));
        //var_dump($arr);
        foreach ($arr as $key => $value) {
            if(intval($value)>0){
                //return '######pass';
                return '('.$value.')';
            }
        }

        //默认值
        $type=$this->getType($string);
        //return $type;
        if($this->_config[$type]){
            return '('.$this->_config[$type].')';
        }
        return '';//'长度';
    }

    /* 
    *  @desc      获取主键
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 19:57:25  
    */ 
    public function getKey($string)
    {
        //do something
        if($this->check_str($string,'key')){  return 'PRIMARY KEY';  }
        if($this->check_str($string,'主键')){  return 'PRIMARY KEY';  }
        return '';
    }

      /* 
    *  @desc      获取是否自增
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 19:57:25  
    */ 
    public function getAuto($string)
    {
        //do something
        if($this->check_str($string,'auto')){  return 'AUTO_INCREMENT';  }
        if($this->check_str($string,'自增')){  return 'AUTO_INCREMENT';  }
        return '';
        
    }
  

    /* 
    *  @desc      获取是否允许空
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 19:57:25  
    */ 
    public function getNull($string)
    {
        if($this->check_str($string,'NULL')){  return 'NULL';  }
        if($this->check_str($string,'允许空')){  return 'NULL';  }
        return 'NOT NULL';
    }

    /* 
    *  @desc      获取默认值
    *  @author    saruri <saruri@163.com>
    *  @date      2020/04/12 21:39:16  
    */ 
    public function getDefault($string)
    {
        $key=$this->getKey($string);
        if($key=='PRIMARY KEY'){
            return '';
        }
        $type=$this->getType($string);
        $arrText=['VARCHAR','CHAR','TEXT'];
        $arrNum=['MEDIUMINT','INT','TINYINT','FLOAT','DOUBLE'];
        $arrTime=['TIMESTAMP'];
        //文本类 
        if(in_array($type, $arrText)){
            return " DEFAULT '' ";
        }

        //数字类
        if(in_array($type, $arrNum)){
            return " DEFAULT 0 ";
        }

        //时间类
        if(in_array($type, $arrTime)){
            return " DEFAULT CURRENT_TIMESTAMP ";
        }

        return '';
        //default current_timestamp
    }

   /* 
   *  @desc      保存表头
   *  @author    saruri <saruri@163.com>
   *  @date      2020/04/12 10:58:20  
   */ 
   public function saveTableHead()
   {
        $string=trim($this->arrResult[0]);
        $arr=explode(" ",$string);
        $this->tableConfig['tableComment']=$arr[0];
        $this->tableConfig['tableName']=end($arr);
        //var_dump($this->tableConfig);
       // echo '保存表头'."\r\n";
   }

   public function run()
   {
        
        
        //保存表头
        self::saveTableHead();
        //保存字段数据
        self::saveTableFiled(); 
        return $this->tableConfig;
        //var_dump($this->tableConfig);
         //echo '处理了数据'."\r\n";
    }


      
} 
//$arr=[];
//$md= new DataTransform($arr);
//$md->run();
// DROP TABLE IF EXISTS test1;
// CREATE TABLE test1 (test int);
// ALTER TABLE test1 DROP test