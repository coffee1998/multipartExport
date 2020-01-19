# multipartExport
项目名称：分片导出数据到csv  
废话不多说，先来看一下效果：  
![image](https://github.com/coffee1998/images/blob/master/export.gif)
演示地址：http://47.92.153.254
#### 安装教程
1.克隆资源库
```shell
git clone https://github.com/coffee1998/multipartExport.git
```
2.安装依赖关系  
进入项目根目录，执行：
```shell
composer update
```
3.编辑.env文件，配置数据库信息  
4.执行命令，生成数据表
```shell
php artisan migrate
```
5.执行命令，填充数据
```shell
php artisan db:seed
```
6.打开浏览器，输入你的项目地址，就可以打开项目了

7.功能是实现了，但是代码写的或许不是很好，献丑了，还请大佬多多指教。

8.加群
加入微信群，每天都会更新Go语言，PHP的进阶学习资源，包括视频和PDF，优质文章分享。一起学习成长。  
![image](https://github.com/coffee1998/images/blob/master/WechatIMG214.jpeg)

如果群失效的可以加我微信（shxzs-888），然后入群  
![image](https://github.com/coffee1998/images/blob/master/WechatIMG195.jpeg)

