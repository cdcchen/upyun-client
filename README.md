# 又拍云SDK

## 安装

```
composer require cdcchen/upyun-client:^1.0.0
```

## 使用教程

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