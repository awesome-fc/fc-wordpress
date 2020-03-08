# 简介

本项目是[一元建站-基于函数计算 + wordpress 构建 serverless 网站](https://yq.aliyun.com/articles/721594)示例工程。

# Serverless 方案与传统自建 web 方案对比

| ITEM | 成本 | 稳定性 | 
| ------ | ------ | ------ |
| 基于 VM 方案 | 默认采购 ecs.t5-lc1m1.small, 22.8元/月 | 服务器和数据库在同一台VM, 均无主备容灾，同时该规格的主机本身性能弱|
| 轻量应用服务器 | 60元/月(1vCPU 1GB 1Mbps 20GB[ssd]) | 服务器和数据库在同一台VM, 均无主备容灾，同时该规格的主机本身性能弱| 
| 函数计算 | sqlite3 版本约为 1元/月 </br>mysql 版本大约 26元/月 | 高 | 

函数计算完整费用详情：

- 每月前 100 万次函数调用免费， 每月前 400000(GB*秒) 费用免费， 函数的内存可以设置为 128M 或者 256M, 因此对于一个一个月访问量低于 100 万次的网站， 该项是免费的

- 对于低成本的网站， 假设一个月的产生的公网流量为 1GB, 0.8元

- NAS, US$0.06/GB/Month, 网站大小为 50M， 即使按 1G 计算， 0.42元

- RDS mysql 最基本的单机版本， 25元/月

[函数计算计费](https://help.aliyun.com/document_detail/137980.html) | [NAS 定价](https://www.alibabacloud.com/zh/product/nas/pricing?spm=a2796.10410706.1184211.1.225e1af83WbMA1)

如上所述， 在低成本网站领域， 函数计算具有十分明显的成本优势，同时还保持了弹性能力，以后业务规模做大以后并没有技术切换成本（可能需要做的只是更换一个更强的关系型数据库), 同时财务成本增长配合预付费也能保持平滑。低成本网站变成高可用高性能网站如丝般顺滑。

# 案例操作步骤

## 准备条件

[免费开通函数计算](https://statistics.functioncompute.com/?title=ServerlessWordpress&theme=ServerlessWeb&author=rsong&src=article&url=http://fc.console.aliyun.com), 按量付费，函数计算有很大的免费额度。

[免费开通文件存储服务NAS](https://nas.console.aliyun.com/)， 按量付费

**可选：** 有一个域名(国内的需要备案， 海外的不需要)， 比如 abc.com， 并将域名 CNAME 解析到 函数计算(FC) 对应的 region

> 如您想在杭州的 region 部署 wordpres 网站， 则将 abc.com CNAME 解析到 12345.cn-hangzhou.fc.aliyuncs.com, 其中 12345 是您的 accountId
> 
> 如果没有域名也没有关系， fun 工具会给您生成一个临时域名


## 3.1	安装最新的 Fun 工具

-	安装版本为8.x 最新版或者10.x 、12.x [nodejs](https://nodejs.org/en/download/package-manager/#debian-and-ubuntu-based-linux-distributions-enterprise-linux-fedora-and-snap-packages)

-	安装 [funcraf](https://github.com/alibaba/funcraft/blob/master/docs/usage/installation-zh.md)

## 3.2 Clone 工程

   `git clone https://github.com/awesome-fc/fc-wordpress.git`

## 3.3 根据需要使用的数据库进入不同的目录

  -	 复制 .env_example 文件为 .env,  并且修改 .env 中的信息为自己的信息

  > 如果使用 mysql 数据库, 参考章节 3.3.1
  
  > 如果使用 sqlite3 数据库, 参考章节 3.3.2 

### 3.3.1 使用 mysql 数据库

- 进入 目录 fc-wp-mysql
  
	```bash
	fun nas init
	fun nas info
	```
	
	> fun nas init: 初始化 NAS, 基于您的 .env 中的信息获取(已有满足条件的nas)或创建一个同region可用的nas
	
	> 如果你没有修改 templata.yml 中的配置 service名字， 那么则可以进入下一步； 如果有修改， 会在当前目录生成新的目录 .fun/nas/auto-default/{serviceName} (fun nas info 可以列出新的目录),  将默认目录下的 .fun/nas/auto-default/fc-wp-mysql/wordpress 的wordpress目录拷贝到 .fun/nas/auto-default/{serviceName} 下， 同时可以删除目录 .fun/nas/auto-default/fc-wp-mysql/wordpress
	
  **可选操作：** 如果您没有自己的域名，可以在这里首先执行一次 fun deploy， 命令行结果输出中会有一个可用的临时域名， 如下图中的 12720569-1986114430573743.test.functioncompute.com, 记录这个域名。

    ```bash
    fun deploy
    ```
    ![image](https://raw.githubusercontent.com/awesome-fc/fc-wordpress/master/png/op1.png)
 
      
- 上传 wordpress 网站到 NAS

	```bash
	fun nas sync
	fun nas ls nas:///mnt/auto/
	```
	
	> `fun nas sync`: 将本地 NAS 中的内容（.fun/nas/auto-default/fc-wp-mysql）上传到 NAS 中的 fc-wp-mysql 目录
	
	> `fun nas ls nas:///mnt/auto/`: 查看我们是否已经正确将文件上传到了 NAS

### 3.3.2 使用 sqlite3 数据库

- 进入 目录 fc-wp-sqlite
  
	```bash
	fun nas init
	fun nas info
	```
	
	> fun nas init: 初始化 NAS, 基于您的 .env 中的信息获取(已有满足条件的nas)或创建一个同region可用的nas
	
	> 如果你没有修改 templata.yml 中的配置 service名字， 那么则可以进入下一步； 如果有修改， 会在当前目录生成新的目录 .fun/nas/auto-default/{serviceName} (fun nas info 可以列出新的目录),  将默认目录下的 .fun/nas/auto-default/fc-wp-sqlite/wordpress 的wordpress目录拷贝到 .fun/nas/auto-default/{serviceName} 下， 同时可以删除目录 .fun/nas/auto-default/fc-wp-sqlite/wordpress
	
  **可选操作：** 如果您没有自己的域名，可以在这里首先执行一次 fun deploy， 命令行结果输出中会有一个可用的临时域名， 如下图中的 12720569-1986114430573743.test.functioncompute.com, 记录这个域名。

    ```bash
    fun deploy
    ```
    ![image](https://raw.githubusercontent.com/awesome-fc/fc-wordpress/master/png/op1.png)
	
- 本地完成安装过程， 初始化 sqlite3 数据库

  - 在目录 .fun/nas/auto-default/fc-wp-sqlite/wordpress 中输入命令：

	```bash
	php -S 0.0.0.0:80
	```
 
  - 修改 host 文件，添加  `127.0.0.1	hz.mofangdegisn.cn`
    > - linux/mac : vim /etc/hosts 
    > - windows7: C:\Windows\System32\drivers\etc
    
    > 其中 hz.mofangdegisn.cn 是您预先准备的域名或者 Fun 为生成的临时域名
    
  - 通过浏览器输入 hz.mofangdegisn.cn， 这个时候没有mysql数据库设置页面，完成 wordpress 安装过程
  
  > 成功安装以后， 这个时候， .fun/nas/auto-default/fc-wp-sqlite/wordpress/wp-content 下面应该有一个 database 的目录， ls -a 查看， 应该有 .ht.sqlite 这个 sqlite3 数据库文件
  
  - 回退 host 文件的修改

	 <font color="#dd0000">注: 中间修改 host 的目的是初始化 sqlite3 数据库的时候， base site url 是提前准备的域名， 而不是 127.0.0.1</font><br /> 
 
- 上传 wordpress 网站到 NAS

	```bash
	fun nas sync
	fun nas ls nas:///mnt/auto/
	```
	
	> `fun nas sync`: 将本地 NAS 中的内容（.fun/nas/auto-default/fc-wp-sqlite）上传到 NAS 中的 fc-wp-sqlite 目录
	
	> `fun nas ls nas:///mnt/auto/`: 查看我们是否已经正确将文件上传到了 NAS


## 3.4 部署函数到FC平台

本地调试OK 后，我们接下来将函数部署到云平台：

- 修改 index.php 中的 $host 中的值， $host 修改为之前步骤中生成的临时域名， 如本例中的 12720569-1986114430573743.test.functioncompute.com

  >  当然， 这里您也可以使用自己的域名， 修改 template.yml 中 DomainName: Auto ,  Auto 修改成您自己的域名，  index.php 中的 $host 中的值也为您自己的域名

- 修改 template.yml LogConfig 中的 Project, 任意取一个不会重复的名字即可, 有两处地方需要更改

   ![image](https://raw.githubusercontent.com/awesome-fc/fc-wordpress/master/png/op2.png)

- 再次执行 fun deploy， 完成最终的部署

2.  登录控制台 [https://fc.console.aliyun.com](https://fc.console.aliyun.com/)，可以看到service 和 函数已经创建成功， 并且 service 也已经正确配置。

3.  通过浏览器打开 Fun 临时生成的域名， 比如本例中的 12720569-1986114430573743.test.functioncompute.com

- mysql 版本数据库， 可以直接跟传统的 wordpress 一样，直接进入安装过程

- sqlite3 版本数据库， 由于之前已经完成初始化，可以直接进入网站首页或网站后台


# FAQ

### Q1: 函数计算能开发高性能高可用网站吗？

A: 可以, 使用函数计算的单实例多并发功能和高性能数据库

- [单实例多并发](https://help.aliyun.com/document_detail/144586.html) 
- 选择高性能关系型数据库，比如高可用的[云数据库POLARDB](https://help.aliyun.com/product/58609.html)

有必要再加上这些优化:

- [预留实例消除冷启动](https://help.aliyun.com/document_detail/140338.html) + [预付费优化成本](https://help.aliyun.com/document_detail/138831.html)
- [极速型 NAS](https://www.aliyun.com/product/nas)
- OSS 对象存储 + CDN 来存储和分发静态资源

> 目前 PHP Runtime 并不支持单实例多并发, 使用 Custom Runtime，可以将基于传统模式 nginx + php-fpm + mysql 开发的网站直接简单无缝迁移到函数计算平台，示例工程 [customruntime-php](https://github.com/awesome-fc/customruntime-php)

> [使用OSS对Wordpress进行图片动静分离](https://yq.aliyun.com/articles/2996)

### Q2: 使用低成本 sqlite3 版本的网站， 冷启动第一次打开很慢怎么办？

A: 用一个 timer trigger 的函数 keep warm 


### Q3: 使用低成本 sqlite3 版本的网站， 能支持多大的 qps？

A: 由 sqlite3 数据库性能决定， 这边有一些压测结果：

![image](https://raw.githubusercontent.com/awesome-fc/fc-wordpress/master/png/pts2.png)

![image](https://raw.githubusercontent.com/awesome-fc/fc-wordpress/master/png/pts1.png)

每次压力增大时候， 都有些冷启动，时间慢点，但是支持从压测结果来看支持 50 QPS 是没有疑问的， 是足够支持一些中小网站的。