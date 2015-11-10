Welcome to PHPCbping!
===================
.. contents::
    :local:
    :depth: 1
    :backlinks: none
    
## <i class="icon-upload"></i> 概要
		内容：一个简单的php框架，主要是满足个人开发使用，面向后端API开发。
		目标：轻量级的仅满足一般开发使用(由开发人员决定)。
		框架：主要是在命令模式基础上扩展而成的。 

架构的序列图：https://www.processon.com/view/link/558a701fe4b0ccebb524bb71
> **Note:** 框架目前没有实现视图层和模型层，只有控制层

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

#### hello world

> * 启动php(5.4+)内置服务器：php -S localhost:8080 <br>
> * 浏览器输入地址：http://localhost:8080/?cmd=HelloWorld <br>
> * 浏览器显示：{"hello":"hello world"}

#### 请求参数

		http://localhost:post/index.php?cmd=test&other=o

* cmd(必需)

>  此参数描述控制的信息,例如
> - cmd=Test:  控制器test，就是说 控制器文件名为：Test.class.php; 类名：Test
> - cmd=test.test1:  控制器test，就是说 控制器文件名为：test1.class.php,此文件在子目录 test/下; 类名：test1 

>  **Note：** 大小写敏感

* 其他参数：用户自定义

#### 配置项
> 请查看：[配置注释](https://github.com/BPing/PHPCbping/blob/dev/Doc/%E9%85%8D%E7%BD%AE%E6%B3%A8%E9%87%8A.md)<br>

#### 项目目录

*  应用目录：[App/](https://github.com/BPing/PHPCbping/tree/dev/App)
*  用户配置目录：[App/Config](https://github.com/BPing/PHPCbping/tree/dev/App/Config)
  
> 用户配置目录是可配置的，具体查看配置说明文档[配置注释](https://github.com/BPing/PHPCbping/blob/dev/Doc/%E9%85%8D%E7%BD%AE%E6%B3%A8%E9%87%8A.md)。<br>
> 用户配置将会覆盖系统默认的配置项。

*  控制器目录：[App/Controllers](https://github.com/BPing/PHPCbping/tree/dev/App/Controllers)

> 控制器目录是可配置的，具体查看配置说明文档[配置注释](https://github.com/BPing/PHPCbping/blob/dev/Doc/%E9%85%8D%E7%BD%AE%E6%B3%A8%E9%87%8A.md)。<br>
> 控制器支持二级子目录的存在<br>
> 所有控制类必需继承父抽象控制类：[Controller](https://github.com/BPing/PHPCbping/blob/dev/PHPCbping/Controller.absclass.php)<br>

```
class test extends \Controller
{
    /**
     * 执行入口
     * @param $arg_context
     */
    function  doExecute(\Context $arg_context)
    {
        $other = $arg_context->Params("other");
        $arg_context->json_echo("hello world:".$other);
    }
}
```

#### 代码覆盖率情况
![code Coverage](https://github.com/BPing/PHPCbping/blob/dev/Doc/TestCoverage.png?raw=true)


## <i class="icon-upload"></i> 结束语
在这里我必须向大家坦白，我实现这个简单的框架主要目的还是为了学习，当然我也存在着想拥有一个简单个人的php开发框架以方便开发一些个人项目的想法。



 			
