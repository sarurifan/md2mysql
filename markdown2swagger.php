<?php
/**
 * 调用数据
 *
 * PHP version 7.2
 *
 * @category  DATABASE
 * @package   SARURI
 * @author    saruri <saruri@163.com>
 * @copyright 2006-2019 saruri
 * @license   MIT LICENSE
 * @link      http://saruri.cn
 * @date      2019/12/28 14:04:42  
 */

namespace saruri;

include_once "base.php";

class Markdown2Swagger extends Base
{
    private static $_config = [
        'MD_SOURCE' => 'api.md',
        'SWAGGER_NAME'=>'2.0',
        'HOST' => '192.168.0.168',
        'PORT'=> '8015',
        'TERMS_Of_SERVICE'=>'192.168.0.168',
        'DESCRIPTION' => '实在不习惯swagger编辑器 还是拥抱markdown 简洁方便的编辑吧',
        'VERSION' => '1.05',
        'BASE_PATH' => '',
        'TITLE'=>'Markdown2Swagger',
        'EMAIL'=>'sarurifan@gmail.com',
        'LICENSE_NAME'=>'Apache 2.0',
        'LICENSE_URL'=>'http://www.apache.org/licenses/LICENSE-2.0.html',

    ];  
 
    public $base,$tabArr;
    public $resultJson;

   //初始化
   public function __construct($config=[])
   {
        //载入默认配置
        if($config){ 
            self::$_config=$config;
        }
        
        $this->init();
   }
      
   //继承上级的初始化 也可以直接初始化 个人习惯init下
   public function init()
   {
    
        parent::__construct(); 
        parent::setConfig(self::$_config);

        $this->tabArr=parent::run();
        //var_dump($this->tabArr);
   }

   //构造tags
   public function buildTags(){

        $arr=$this->tabArr['tag'];
        $i=0;
        foreach ($arr as $key => $value) {
          
            if($value['path']){
               
                $this->resultJson['tags'][$i]['name']=$value['path'];
                $this->resultJson['tags'][$i]['description']=$value['name'];
                $i++;
            }
          
        }



   }

   //构造基础配置  根据传递来的数组
   public function buildConfig(){ 
        //var_dump($this->tabArr);
        
        $arr=explode("\r\n",$this->tabArr['info']);
        $string='';
        foreach ($arr as $key => $value) {
            $string.= $value."</br>";
        }
        $this->tabArr['info']= $string;
        self::$_config['TITLE']=$this->tabArr['title'];
        self::$_config['DESCRIPTION']=$this->tabArr['info'];

   } 


   //构造基础swaggerJson
   public function buildBaseJson(){
  

    $swagger = array(
        'swagger' => self::$_config['SWAGGER_NAME'],
        'info'		=> array(
            'description'	=> self::$_config['DESCRIPTION'],
            'version'		=> self::$_config['VERSION'],
            'title'			=> self::$_config['TITLE'],
            'termsOfService'=> self::$_config['TERMS_Of_SERVICE'],
            'contact'		=> array(
                'email'		=> self::$_config['EMAIL'],
            ),
            'license'		=> array(
                'name'		=> self::$_config['LICENSE_NAME'],
                'url'		=> self::$_config['LICENSE_URL'],
            )
        ),
        'host'		=> self::$_config['HOST']. ':' . self::$_config['PORT'],
        'basePath'	=> self::$_config['BASE_PATH'],
        'tags'		=> $this->resultJson['tags'],
        'schemes'	=> [
            'http'
        ],
        'paths'		=> $this->resultJson['paths'],
        'securityDefinitions'=> array(         
        ),
        'definitions'=> array(
        ),
        'externalDocs'=> array(
            'description'	=> '查找详细说明',
            'url'			=> 'https://github.com/sarurifan/markdown2swagger'
        )
    );

    $this->resultJson= $swagger;
    //return $swagger;

   }
   

   //构造具体接口数据
   public function buildPath(){

        foreach ($this->tabArr['tag'] as $key => $value) {
            
            //$this->resultJson['paths'][$value['']]
            $this->buildApiDetail($value);
            //echo '11';
        }

        //var_dump($this->tabArr);

        //exit("666");
   }

   //构造详细数据
   public function buildApiDetail($arr2){
       //var_dump($arr2);  
        //exit($arr2['path']);

        foreach ($arr2['api'] as $key => $value) {
            # code...
            //var_dump($value);
           // exit("测试");

            $arr['tags']=[ $arr2['path']];
            $arr['summary']=$value['name'];
            $arr['description']=$value['api_request'].$value['api_request_field_info'];
            $arr['operationId']=$value['api_key'];
            // $arr['consumes'] = [
            //     //"multipart/form-data"
            // ];
            $arr['produces'] = [    
                "application/json",
                "application/xml"
              ];
            $arr['parameters']=$this->buildParam($value['api_param']);
            $arr['responses']=$this->buildRequest($value);
            $arr['security']=$this->buildSecurity();
            $value['type']=strtolower($value['type']);
            $this->resultJson['paths'][$value['url']][$value['type']]=$arr;
            //var_dump($arr);
        }

        //exit("99");
   }

   //构造属性
   public function buildParam($arr2)
   {
       //var_dump($arr2);

       if(is_array($arr2) && !empty($arr2)){ 
            $i=0;
            foreach ($arr2 as $key => $value) {
                if($value){
                    $arr3[$i]['name']=$key;
                    $arr3[$i]['in']='formData';
                    $arr3[$i]['required']=true;
                    $arr3[$i]['description']=$value;
                    $arr3[$i]['type']='string';
                    $i++;
                }
            
            }
        }
       
      
       return $arr3;
       //var_dump($arr3);
       //exit("测试字段");
       //$arr['name']=$arr
    
   }

    //构造输出
    public function buildRequest($arr)
    {
       
        //var_dump($arr);
       // exit("构造输出");
        $responses=[
      
            "200" => [
              "description" => "successful operation",
              "schema" => [
                "type" => "array",
                "items" => [
                  "$ref" => "#/definitions/Pet"
                ],
                "example"=>$this->buildExample($arr),

              ]
            ],
            "400" => [
              "description" => "Invalid status value"
            ]
          
            ];
       
        return $responses;
    }

    //构造Example 目前用
    public function buildExample($arr)
    {
        //exit($arr);
       $arr2=[ "status" => "状态码",
        "msg" => "服务端返回消息",
        "data" => [
            "id"=>"名片id",
            "uid"=>"用户id",
        ]
        ];
        //var_dump($arr['api_request']);
        //var_dump($arr['api_example']);
        //exit("测试");
        if($arr['api_request']!=false){
            foreach ($arr['api_request'] as $key => $value) {
                if($value){
                     if($arr['api_example'][$key]){
                         $arr['api_request'][$key]=$arr['api_example'];
                     }
                }
             }
        }


        return  $arr['api_request'];
    }

    //构造sec
    public function buildSecurity(){
        $security = [
            "petstore_auth" => [
                "write:pets",
                "read:pets"
              ]
            ];
        return $security;
          
    }

   //输出成json
   public function toJson(){
        $json=json_encode($this->tabArr,true);
        exit($json);
   }



   public function run()
   {
        //var_dump($this->tabArr);
        $this->buildConfig();
        $this->buildTags();
        $this->buildPath();
        $this->buildBaseJson();
       

        $json=json_encode($this->resultJson,true);
        header('content-type:application/json');
        file_put_contents('test.json', $json);
        //exit($json);
        //主流程
        //var_dump($json);
        //exit('测试1');
        header('location: http://doc.sianwan.com/swagger/'); 
    }
      
} 

$md= new Markdown2Swagger();
$md->run();
