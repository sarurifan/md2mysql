<?php
// +----------------------------------------------------------------------
// | markdown2swagger
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://saruri.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( MIT LICENSE )
// +----------------------------------------------------------------------
// | Author: saruri <sarurifan@gmail.com>
// +----------------------------------------------------------------------

namespace saruri;

/**
* markdown转换为格式接口 做准备  数据处理 返回数组格式 供前级进行 构造swagger格式
* 约定格式 1. " --- " 为每个接口分隔
* 约定格式 2. 第一个为整个系统接口介绍 之后都是具体接口
* 约定格式 3. 第一个里面 " ## 分类介绍 " 为介绍 接口的主分类 TAG
* 约定格式 4. #### 接口名称|方式|路径
* 约定格式 5. ###### 传输参数  传输参数表
* 约定格式 6. ###### 返回参数  返回参数表
* 约定格式 7. ``` 到 ``` 为 接口示例说明 或其他 根据约定来
*
*/
class Base
{
    /**
     * @var array 配置参数
     */
    private static $_config = [
        'MD_SOURCE' => 'api.md',
        'DB_NAME'=> 'thinkssns_com',
        'DB_USER' => 'thinkssns_com',
        'DB_PWD' => 'NHGMJBW7FfdF2etF',
    ];

    public $mdFile='';
    public $orm;
    public $tables;
    public $conn;
    public $result; //返回的最终数组

    //初始
    public function __construct()
    {
        //各种配置
        date_default_timezone_set('Asia/Shanghai');
    }
    
    //重载配置
    public function setConfig($config)
    {
        self::$_config=$config;
    }
    


    //读取数据
    public function readFile()
    {
        $this->mdFile =  file_get_contents(self::$_config['MD_SOURCE']) or die("Unable to open file!");
        $this->mdFile =str_replace("---|---", "======", $this->mdFile);
        $this->mdFile =str_ireplace("|API|", "|POST|", $this->mdFile);//原文档有点问题 应该写|POST| |GET| 后期按约定的写就没这问题了
        $this->arr=explode('---', $this->mdFile);

        $this->getApiBaseInfo();

        
        unset($this->arr[0]);
        //var_dump($this->arr);
        //var_dump($this->arr[1]);
        //exit('结束读取');
    }
     
    

    //获取接口名
    public function getApiName()
    {
    }

    //获取接口标识 tag
    public function getApiTag()
    {

        $arr  = explode("## 分类介绍", $this->info);
        $arr2 = explode("##", $arr[1]);
        $arr3 = explode("-", $arr2[0]);
        unset($arr3[0]);

        foreach ($arr3 as $key => $value) {
            $arr4=explode("/", $value);
            $this->result['tag'][$arr4[1]]['name'] = trim($arr4[0]);
            $this->result['tag'][$arr4[1]]['path'] = trim($arr4[1]);
            $this->result['tag'][$arr4[1]]['info'] = trim($arr4[2]);
        }
        
        unset($arr,$arr2,$arr3,$arr4);
        
    }

    //获取接口 完整路径
    public function getApiUrl()
    {
    }

    //获取项目介绍 构造数组
    public function getApiBaseInfo()
    {
        //获取项目的各种介绍数据
        $this->info=$this->arr[0];

        //获取主题
        $arr  = explode("##", $this->info);
        $title=str_replace("#","", $arr[0]);
        $this->result['title']=trim($title);
        
        //获取项目介绍
        $this->result['info']=trim($arr[1]);
        unset($arr);

    }




    //获取具体接口名字和介绍
    public function getApiInfo($string)
    {
       
        $arr2 =$this->getApiTitle($string);
        // $this->result['tag']
       //var_dump($arr2);
       return $arr2;
    }

    //获取具体接口名字和介绍 返回数组 各种情况都出来了 .md写的好随意 
    //正则不熟 那就来个数组地狱吧
    public function getApiTitle($string)
    {
        //$string=str_replace("=","",$string);
        $arr  = explode("######", $string);
        $arr2 = explode("|", $arr[0]);
        $arr3['name']= trim(str_replace("####","",$arr2[0]));
        $arr3['name']=trim(str_replace("-","", $arr3['name']));
        $arr7=explode("####",$arr3['name']);
        $arr3['name']=trim($arr7[0]);


        $arr3['type']= trim($arr2[1]);

        $arr5=explode(">",$arr2[2]);
        $arr3['url']= trim($arr5[0]);
        $arr9=explode("####",$arr3['url']);
        $arr3['url']=trim($arr9[0]);

        $arr4 = explode("/", $arr2[2]);
        $arr3['tag_key']=trim($arr4[1]);

        $arr6=explode(">",$arr4[2]);
        $arr8=explode("####",$arr6[0]);
        $arr3['api_key']=trim($arr8[0]);


        $arr3['api_info']=trim($arr5[1]);

        $arr3['api_param']=$this->getApiParam($string);
        $arr3['api_request']=$this->getApiRequest($string);
        $arr3['api_example']=$this->getApiExample($string);
        $arr3['api_request_field_info']=$this->getApiRequestFieldInfo($string);
        unset($arr,$arr2,$arr4,$arr5,$arr6,$arr7,$arr8,$arr9);
        return $arr3;
    }

    //获取传输参数
    public function getApiParam($string){
        $substr='###### 传递参数';
        $nums=substr_count($string,$substr);
            if ($nums<1){ return false; exit($nums.'###### 传递参数'.$string); }
            if ($nums>0){
                $arr=explode($substr,$string);
                $arr2=explode('======',$arr[1]);
                $arr3=explode('######',$arr2[1]);
                $arr4=explode("\r\n", $arr3[0]);
               
                foreach ($arr4 as $key => $value) {
                    $value=trim($value);
                    if($value){
                        $arr5=explode("|", $value);

                        $arr6[$arr5[0]]=trim($arr5[1]);
                    }
                }

                return $arr6;
                //var_dump($arr6);
                //var_dump(trim($arr3[0]));
                //exit();
            }
        return '';
        //exit($string);
    }

    //获取返回参数
    public function getApiRequest($string){
        //exit($string);
        //exit("获取request");
        $substr='###### 返回参数';
        $nums=substr_count($string,$substr);
        if ($nums<1){ return false;  exit($nums.'###### 传递参数'.$string); }

        if ($nums>0){
            $arr=explode($substr,$string);
            $arr2=explode('======',$arr[1]);
            $arr3=explode('```',$arr2[1]);
            $arr4=explode("\r\n", $arr3[0]);
            foreach ($arr4 as $key => $value) {
                $value=trim($value);
                if($value){
                    $arr5=explode("|", $value);

                    $arr6[$arr5[0]]=trim($arr5[1]);
                }
            }
            return $arr6;

            //新增的 让返回的是字符串 而不是数组了
            $str='';
            $str.='返回参数 | 返回参数说明<br>';
            foreach ($arr6 as $k => $val) {
                # code...
                $str.='<em>'.$k.'</em> | '.$val.'<br>';
            }

            

            //var_dump($arr6);
            //exit("测试");
                return $str;
            
          
            exit($string);   
        }
        
    }

    //获取示例参数
    public function getApiExample($string){
        
        $arr=explode("示例说明",$string);
        if($arr[1]){
            $arr2=explode("=====",$arr[1]);
            $arr3=explode("```",$arr2[1]);
            $arr4=explode("\r\n",$arr3[0]);
            foreach ($arr4 as $key => $value) {
                //var_dump($value);
               // echo '--->'.$key.'<br>';
                if($value && $value !="="){
                    $arr10=explode("|",$value);
                    $arr5[$arr10[0]]=$arr10[1];
                }
                
            }
        }
        $arr6=explode("|",$arr2[0]);
        $keyName=trim($arr6[0]);

        $arr7[$keyName]=$arr5;
        //var_dump($arr2);
        return $arr7;
        var_dump($arr7);
        exit("测试示例".$keyName);
        exit($string);
    }
    

    //获取返回参数字段说明
    public function getApiRequestFieldInfo($string){
        if($string){
            $arr=explode('```',$string);
            if($arr[1]){
                //$arr[1]=str_replace(" ","",$arr[1]);
                //$arr[1]=str_replace("/r/n","",$arr[1]);
                return trim($arr[1]);
            }
        }
        return '';
    }




    //执行
    public function run()
    {
        $this->readFile();
        $this->getApiTag();

        //var_dump($this->result);
        foreach ($this->arr as $key => $value) {

            $arr=$this->getApiInfo($value);
            $this->result['tag'][$arr['tag_key']]['api'][$arr['api_key']]=$arr;

            //var_dump($arr);
           // echo $arr['tag_key']."<br>";

        }

        //var_dump($this->result);
        
        return $this->result;
    }

   
}

//$md= new Base();
//$md->run();
