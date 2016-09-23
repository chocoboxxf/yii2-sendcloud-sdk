<?php
/**
 * 模板发送接口
 * User: chocoboxxf
 * Date: 16/9/23
 */
namespace chocoboxxf\SendCloud\Tests;

class SendTemplateMailTest extends BaseTest
{
    public function testSend()
    {
        $subject = '';
        $template = 'TEMPLATE_NAME';
        $to = ['user_a@company.com', ];
        $from = 'admin@company.com';
        $fromName = 'Admin';
        $templateData = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];
        $attachments = [
            __DIR__ . '/data/test.txt',
            __DIR__ . '/data/test.pdf',
        ];
        var_dump($this->client->sendTemplateMail($template, $to, $from, $fromName, $subject, $templateData, $attachments));
    }
}
