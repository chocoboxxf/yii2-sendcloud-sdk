<?php
/**
 * 普通发送接口
 * User: chocoboxxf
 * Date: 16/9/23
 */
namespace chocoboxxf\SendCloud\Tests;

class SendNormalMailTest extends BaseTest
{
    public function testSendPlain()
    {
        $subject = '测试plain邮件';
        $content = 'this is a test plain text';
        $to = ['user_a@company.com', ];
        $from = 'admin@company.com';
        $fromName = 'Admin';
        $templateData = [
        ];
        $attachments = [
            __DIR__ . '/data/test.pdf',
        ];
        var_dump($this->client->sendNormalMail($subject, $content, $to, $from, $fromName, $templateData, $attachments));
    }

    public function testSendHtml()
    {
        $subject = '测试html邮件';
        $content = '<html><h1>this is a test html text</h1></html>';
        $to = ['user_a@company.com', ];
        $from = 'admin@company.com';
        $fromName = 'Admin';
        $templateData = [
        ];
        $attachments = [
            __DIR__ . '/data/test.txt',
        ];
        var_dump($this->client->sendNormalMail($subject, $content, $to, $from, $fromName, $templateData, $attachments));
    }

    public function testSendHtmlWithData()
    {
        $subject = '测试带变量的html邮件';
        $content = '<html><h1>this is a test %key% html text</h1></html>';
        $to = ['user_a@company.com', ];
        $from = 'admin@company.com';
        $fromName = 'Admin';
        $templateData = [
            'key' => 'value',
        ];
        $attachments = [
            __DIR__ . '/data/test.pdf',
        ];
        var_dump($this->client->sendNormalMail($subject, $content, $to, $from, $fromName, $templateData, $attachments));
    }
}
