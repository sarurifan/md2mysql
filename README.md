# 方便建表用
## 特意做了这个 直接markdown 转成sql 方便
# 需求 想法

把markdown 格式的数据 转换成建表语句

要求 简单 方便 好用 markdown不能太复杂 

# 基础约定
```
# 号为 表名 然后接空格 注释表名
例 # sys_think_php  系统基础表

表头固定 三列

```
>- 示范md
> # sys_think_php  系统基础表
> 字段|参数|备注
> ---|---|---
> id| int 11 key| 表id
> name| varchar 255 notnull 不为空|姓名



# 伪代码
```
// 初始化 
//配置参数
//构造 文本框
//输出前台页面
//做回传页面js 

//接收变量
//转换成alert table
//名字处理
//类型处理
//注释处理
//主键处理
//返回 json 数据

- 可选项
//存储cookies 存入用户的 json文本结果
//列表输出 

```

# 流程设计

> # 流程设计 主流程 CLASS MAINSERVICE
> ```
> graph LR
> B1['用户文本框']--'传输代码'-->B2['服务端']
> B2--'处理数据返回sqlJSON'-->B3['在用户文本框渲染']
> ```

> # 流程设计 数据转换流程 CLASS DATATRANSFORM
> ```
> graph LR
> A['服务端']-->B1
> B1['接收数据']--'# 号获取表名+备注 '-->B2['表资料数组']
> B2--'提取基础字符串 '-->B3['字段名数组']
> B3--'构造数组'-->B4['转换成sql']
> B4--'输出'-->B7['json']
> ```

> # 流程设计 获取字段流程 CLASS GETFIELD
> ```
> graph TD
> B1['接收到的数据']--'# 号获取表名+备注 '-->B2['表资料数组']
> B1['接收到的数据']--'判断'-->C['异常']
> B2--'提取基础字符串 '-->B3['字段名数组']
> B3--'提取'-->B4['字段名']
> B3--'提取'-->B41['字段注释']
> B3--'提取'-->B42['字段属性字符串']
> B42--'空格切分猜测提取'-->A421['字段类型']
> B42--'空格切分猜测提取'-->A422['字段长度']
> B42--'空格切分猜测提取'-->A423['是否主键']
> B42--'空格切分猜测提取'-->A424['是否允许空']
> B42--'空格切分猜测提取'-->A425['是否浮点']
> B42--'空格切分猜测提取'-->A426['预留其他判断']
> ```

> # 流程设计 保存并列表json文件 CLASS JSONFILES
> 可选项 再议


# 实施 测试

演示 地址 :https://api.saruri.cn/

# 介绍
### 目前支持字段类型参数
- tinyint
- mediumint
- int
- float
- double
- varcha
- char
- text
- 整数
- 文本
- datetime
- 时间戳
- 时间
- 日期


### 支持的 字段参数
- 支持 设置默认值 数值类 默认 0 ,字符串类 默认 ''
- 支持 空 判定  默认为不为空 "null ,not null"
- 支持 key 主键参数  "key 主键 "
- 支持 auto 参数 自增参数 "auto 自增"
- 支持 位数自动判断
- 支持 innodb
- 支持 删除同名表重建

# 改进
其他数据库看实际业务情况支持吧.
# release版本FTP账号资料
目前1.0
