# 又拍云SDK

## 安装

```
composer require cdcchen/upyun-client:^1.0.0
```

## 文件操作使用教程

### 实例化 UpYunClient

```php
use cdcchen\upyun\UpYunClient;

$bucketName = '';
$username = '';
$password = '';
$client = new UpYunClient($bucketName, $username, $password);
```

### 上传文件

```php
try {
    $result = $client->writeFile($distFile, $srcFile);
    print_r($result);
} catch (\cdcchen\upyun\ResponseException $e) {
    echo $e->getMessage(), $e->getCode(), PHP_EOL;
} catch (Exception $e) {
    echo $e->getCode(), $e->getMessage(), PHP_EOL;
}
```

### 读取文件

```php
$data = $client->readFile($file);
```

### 删除文件

```php
$data = $client->deleteFile($file);
```

### 获取文件信息

```php
$data = $client->getFileInfo($file);
```

### 创建目录

```php
$result = $client->createDir($path, $mkdir = true);
```

### 删除目录

```php
$result = $client->deleteDir($path);
```

### 读取目录

```php
$data = $client->readDir($path);
```

### 获取bucknet存储使用量

```
$data = $client->getBucketUsage();
```


## Url 作图接口

```php
$delimiter = '!';
$maker = new UrlImageMaker($url, $delimiter);
$maker->fw(200)->scale(2);
$url = $maker->getUrl();
```

所有url做图接口方法直接使用又拍云官方文档对应的作图参数名称。

具体查看：<http://docs.upyun.com/cloud/image/#_7>