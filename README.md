# lumen 框架自用版
基于 Lumen (5.4.7) (Laravel Components 5.4.*) 版本

Exceptions、Log、Facades、artisan

###安装
1、 环境变量配置

    cp .env_example .env

2、依赖包安装

    composer install

3、日志文件权限设置

    chmod 777 storage

###使用
1、创建model命令

    php artisan make:modules {moduleName} [tableName]