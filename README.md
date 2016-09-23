# yii2-sendcloud-sdk
基于Yii2实现的SendCloud API SDK（目前开发中）

环境条件
--------
- >= PHP 5.4
- >= Yii 2.0
- >= GuzzleHttp 6.0

安装
----

添加下列代码在``composer.json``文件中并执行``composer update --no-dev``操作

```json
{
    "require": {
       "chocoboxxf/yii2-sendcloud-sdk": "dev-master"
    }
}
```

设置方法
--------

```php
// 全局使用
// 在config/main.php配置文件中定义component配置信息
'components' => [
  .....
  'mail' => [
    'class' => 'chocoboxxf\SendCloud\SendCloud',
    'apiUser' => 'API_USER', // API User
    'apiKey' => 'API_KEY', // API Key
    'defaultFrom' => 'default@default.com', // 缺省发件人地址
    'defaultFromName' => 'default', // 缺省发件人名称
  ]
  ....
]
// 代码中调用
$result = Yii::$app->mail->sendTemplateMail(
    'TEMPLATE_NAME',
    ['user_a@company.com', 'user_b@company.com'],
    'admin@company.com',
    'admin',
    'Welcome Letter',
    ['key1' => 'value1 for all', 'key2' => ['value2 for a', 'value2 for b']],
    ['/path/to/file1', '/path/to/file2']
);
....
```

```php
// 局部调用
$mailService = Yii::createObject([
    'class' => 'chocoboxxf\SendCloud\SendCloud',
    'apiUser' => 'API_USER', // API User
    'apiKey' => 'API_KEY', // API Key
    'defaultFrom' => 'default@default.com', // 缺省发件人地址
    'defaultFromName' => 'default', // 缺省发件人名称
]);
$result = $mailService->sendTemplateMail(
    'TEMPLATE_NAME',
    ['user_a@company.com', 'user_b@company.com'],
    'admin@company.com',
    'admin',
    'Welcome Letter',
    ['key1' => 'value1 for all', 'key2' => ['value2 for a', 'value2 for b']],
    ['/path/to/file1', '/path/to/file2']
);
....
```

使用示例
--------

普通发送接口

```php
$subject = '测试邮件';
$content = '<html><h1>this is a test %key% html text</h1></html>';
$to = ['user_a@company.com', 'user_b@company.com'];
$from = 'admin@company.com';
$fromName = '管理员';
$templateData = [
    'key' => 'value',
];
$attachments = [
   '/path/to/file1',
];
$result = Yii::$app->mail->sendNormalMail($subject, $content, $to, $from, $fromName, $templateData, $attachments);
if ($result['result'] === true) {
    // 正常情况
    // 返回数据格式
    // {
    //   "statusCode": 200,
    //   "info": {
    //     "emailIdList": [
    //       "1447054895514_15555555_32350_1350.sc-10_10_126_221-inbound0$user_a@company.com",
    //       "1447054895514_15555555_32350_1350.sc-10_10_126_221-inbound1$user_b@company.com"
    //     ]
    //   },
    //   "message": "请求成功",
    //   "result": true
    // }
    ....
} else {
    // 出错情况
    // 返回数据格式
    // {
    //   "statusCode": 40863,
    //   "info": {},
    //   "message": "to中有不存在的地址列表. 参数to: user_a@company.com",
    //   "result": false
    // }
    ....
}
....
```

模板发送接口

```php
$subject = '测试邮件';
$template = 'TEMPLATE_NAME';
$to = ['user_a@company.com', 'user_b@company.com'];
$from = 'admin@company.com';
$fromName = '管理员';
$templateData = [
    'key1' => 'value1 for a and b',
    'key2' => ['value2 for a', 'value2 for b']
];
$attachments = [
   '/path/to/file1',
];
$result = Yii::$app->mail->sendTemplateMail($template, $to, $from, $fromName, $subject, $templateData, $attachments);
if ($result['result'] === true) {
    // 正常情况
    // 返回数据格式
    // {
    //   "statusCode": 200,
    //   "info": {
    //     "emailIdList": [
    //       "1447054895514_15555555_32350_1350.sc-10_10_126_221-inbound0$user_a@company.com",
    //       "1447054895514_15555555_32350_1350.sc-10_10_126_221-inbound1$user_b@company.com"
    //     ]
    //   },
    //   "message": "请求成功",
    //   "result": true
    // }
    ....
} else {
    // 出错情况
    // 返回数据格式
    // {
    //   "statusCode": 40863,
    //   "info": {},
    //   "message": "to中有不存在的地址列表. 参数to: user_a@company.com",
    //   "result": false
    // }
    ....
}
....
```