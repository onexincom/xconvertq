# DiscuzX3.4转换DiscuzQ 3.0工具

### 介绍
Discuz! X3.4 转换 Discuz! Q 3.0 转换测试，代码优化记录于此。


### 前提条件

已部署 Discuz! Q3.0 和 Discuz! X3.4。
具备 PHP 7.2.5 及以上环境的主机。

### 安装教程

1.  使用composer安装该项目xconvertq文件：```composer create-project onexin/discuzxtq```
2.  打开Discuz!Q官方的教程：https://discuz.com/docs/Discuzto.html
3.  继续操作，直到成功。

### 值得一提的事：
1、转换提示编码错误BUG：
打开 config/datebase.php文件，修改
```php
        'charset'   => 'utf8', //Q数据编码
        'collation' => 'utf8mb4_unicode_ci', //Q数据库字符集
```
修改为：
```php
        'charset'   => 'utf8mb4', //Q数据编码
        'collation' => 'utf8mb4_unicode_ci', //Q数据库字符集
```

2、本转换工具优化了[code]标签相关代码的转换，将Q3.0贴子类型type值设为99解决回贴空白的BUG。

3、转换中可能会用到的命令，一次没成功再来一次，直到成功。
```sh
# 开始转换所有数据
> php discoa app:xtq

# 清空Q数据库
> php discoa app:clean cleanDatabase
```

### 交流实践
ONEXIN大数据新手Q群：189610242
Github: https://github.com/onexincom/xconvertq
Gitee: https://gitee.com/ONEXIN/xconvertq
