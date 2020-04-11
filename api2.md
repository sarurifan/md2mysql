
# 仿牧游宝典项目API 1.01
## 介绍
- 系统基于 ThinkSNS v4 开发
- 正常 T4API接入地址  api.php
- API默认需要用户登录后，传输正确的 oauth_token和  oauth_token_secret才能正常访问，
- 但是，api也有不用登陆的 api，这种 api我们称之为白名单  api，添加白名单 api在
/config/api.inc.php中配置
- 以下 API地址格式是/mod/act的形式书写，例如  api.php?mod=Oauth&act=authorize将以
/Oauth/authorize的形式描述。
- API按照按照功能文件排列书写，不分先后，可能有些功能会被岔开！
- API参数传递，支持 get和  post混合传输，但是，mod和  act必须  get方式，其他参数建议 post方式传输
- 随着功能开发 文档会逐步更新 版本迭代
- 目前版本 1.22
- 更新时间 2019 12 23

## 分类介绍
- 权限类接口 /Oauth/  白名单
- 系统类接口/System/
- 消息类接口 /Message/
- 个人主页类接口 /User/
- 任务类接口 /Task/
- 礼物商城类接口 |/Gift/
- 找人类接口 /FindPeople/
- 微博类接口 /Weibo/
- 标签类接口 /Tag/
- 举报类接口 /Denounce/
- 积分充值类接口 /Credit/
- 签到类接口 /Checkin/
- 频道相关类接口 /Channel/
- 全局系列接口  /Public/ ==新增==  白名单
- 名片库系列接口  /Namecard/ ==新增==  
## 注意提示

> 接口 模型为大写首字母 大小写不符会不认 示例 :Outh

> 动作为小写字母开头 驼峰式 示例 : abcDef

---


#### 用户登录 |API| /Oauth/authorize



###### 传递参数
参数名称|参数说明
---|---
login|登陆参数，支持 email，uname，phone三种登陆名
password|用户密码

###### 返回参数：
参数名称|参数说明
---|---
status|状态码，0代表失败，1代表成功
msg|消息
uid|登陆成功后的用户  uid
oauth_token|后续API需要传递的  token
oauth_token_secret|和   token成对传输的  token密钥


---
#### 用户退出登录 |API|/Oauth/logout

###### 传递参数
参数名称|参数说明
---|---
login|退出的用户，支持 uid，uname，phone，email


###### 返回参数：
参数名称|参数说明
---|---
status|xxx状态码，1退出成功，0退出失败
msg|消息

---
##### 获取手机验证码（仅用于注册用户）|API| /Oauth/sendCodeByPhone
###### 传递参数
参数名称|参数说明
---|---
login|该字段可以是四个类型 1.UID，2.email，3.phone，4.uname

###### 返回参数
字Y名称|字段说明
---|---
status|状态码
message|服务端消息

###### 状态码参数：
状态码|状态码说明
---|---
1|发送成功
0|用户没有绑定手机号码或者用户不存在
-1|发送短信发生了错误，具体看  message

---
#### 校验手机验证码是否正确|API|/Oauth/checkCodeByPhone

###### 传递参数
参数名称|参数说明
---|---
login|该字段可以是四个类型 1.UID，2.email，3.phone，4.uname
code|验证码

###### 返回参数
字Y名称|字段说明
---|---
status|状态码
message|消息

###### 状态码参数：
状态码|状态码说明
---|---
1|修改成功
0|用户不存在或者没有绑定手机号码
-1|验证码不能为空
-2|密码达不到要求，具体看  message
-3|发送短信失败，具体看  message
-4|修改失败

---
##### 保存用户新密码|API|/Oauth/saveUserPasswordByPhone
###### 传递参数
参数名称|参数说明
---|---
login|用户字段，支持 uid，email，phone，uname
password|要保存的用户密码
code|验证码

###### 返回参数
字Y名称|字段说明
---|---
status|状态码
message|消息

###### 状态码参数：
状态码|状态码说明
---|---
1|修改成功
0|用户不存在或者没有绑定手机号码
-1|验证码不能为空
-2|密码规范，具体看  message
-3|验证码错误，具体看  message
-4|修改失败



