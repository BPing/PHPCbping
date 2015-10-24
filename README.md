Welcome to PHPCbping!
===================
You can insert a table of contents using the marker `[TOC]`:

[TOC]


## <i class="icon-upload"></i> 概要
		内容：一个简单的php框架，主要是满足个人开发使用。
		目标：轻量级的仅满足一般开发使用(由开发人员决定)。
		框架：主要是在命令模式基础上扩展而成的。 

架构的序列图：https://www.processon.com/view/link/558a701fe4b0ccebb524bb71

## <i class="icon-upload"></i> 规范
* 目前没有遵守也未考虑遵守[PSR](http://www.php-fig.org/)编码规范。
* 请看文档[编码风格要求](https://github.com/BPing/PHPCbping/blob/dev/Doc/%E7%BC%96%E7%A0%81%E9%A3%8E%E6%A0%BC%E8%A6%81%E6%B1%82.md)，此文档里面描述本项目所遵守的规则。因为所有规则都是开发人员定义，所以并不完善，望指正！
* 开发人员务必认真阅读[Doc/](https://github.com/BPing/PHPCbping/tree/dev/Doc)目录下的文档文件。所有开发文档资料都放在此目录下

## <i class="icon-upload"></i> 使用
#### 单文件入口：index.php
```
<?php
require_once 'PHPCbping/PHPCbping.class.php';
//启动程序
PHPCbping::start();
```

#### 请求参数

		http://localhost:post/index.php?cmd=test&other=o

* cmd(必需)

>  此参数描述控制的信息,例如
> - test  控制器test，就是说 控制器文件名为：Test.class.php; 类名：Test
> - test.test1  控制器test，就是说 控制器文件名为：Test1.class.php,此文件在子目录 test/下; 类名：Test1 

* 其他参数：用户自定义

#### 配置项
> 请查看：[配置注释]()

#### 项目目录

*  应用目录：[App/]()
*  用户配置目录：[App/Config]()
  
> 用户配置目录是可配置的，具体查看配置说明文档[配置注释]()。
> 用户配置将会覆盖系统默认的配置项。

*  控制器目录：[App/Controllers]()

> 控制器目录是可配置的，具体查看配置说明文档[配置注释]()。<br>
> 控制器支持二级子目录的存在<br>
> 所有控制类必需继承父抽象控制类：[Controller](https://github.com/BPing/PHPCbping/blob/dev/PHPCbping/Controller.absclass.php)

```
class test extends \Controller
{
    /**
     * 最终执行入口
     * @param $arg_context
     */
    function  doExecute(\Context $arg_context)
    {
        $other = $arg_context->Params("other");
        $arg_context->json_echo("hello world:".$other);
    }
}
```

## <i class="icon-upload"></i> 结束语
 			
