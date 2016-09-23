<?php
/**
 * SendCloud SDK
 * User: chocoboxxf
 * Date: 16/9/23
 */
namespace chocoboxxf\SendCloud;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use yii\base\Component;
use yii\base\InvalidConfigException;

class SendCloud extends Component
{
    /**
     * API接口
     */
    const API_MAIL_SEND_TEMPLATE = 'mail/sendtemplate'; // 模板发送
    const API_MAIL_SEND = 'mail/send'; // 普通发送

    /**
     * 接口Base URL
     * @var string
     */
    public $apiUrl = 'http://api.sendcloud.net';
    /**
     * 接口版本
     * @var string
     */
    public $apiVersion = 'apiv2';
    /**
     * API User
     * @var string
     */
    public $apiUser;
    /**
     * API Key
     * @var string
     */
    public $apiKey;
    /**
     * 默认发件地址
     * @var string
     */
    public $defaultFrom = '';
    /**
     * 默认发件人名称
     * @var string
     */
    public $defaultFromName = '';
    /**
     * API Client
     * @var \GuzzleHttp\Client
     */
    public $apiClient;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset($this->apiUser)) {
            throw new InvalidConfigException('请先配置API User');
        }
        if (!isset($this->apiKey)) {
            throw new InvalidConfigException('请先配置API Key');
        }
        $this->apiClient = new Client([
            'base_uri' => sprintf('%s/%s/', $this->apiUrl, $this->apiVersion),
        ]);
    }

    /**
     * 模板发送
     * @param string $subject 邮件标题
     * @param string $content 邮件内容,可以是普通文本,或者HTML格式文本
     * @param array|string $to 接收地址(列表)
     * @param string $from 发件人地址
     * @param string $fromName 发件人名称(可选)
     * @param array $templateData 模板数据(可选),key-value形式,key不包含%
     *     如有多个收件地址,相同key的value不同,value可以为数组形式,如: 'key' => ['value1', 'value2']
     *     如有多个收件地址,相同key的value也相同,value同单个收件地址的情况,如: 'key' => 'value'
     * @param array $attachments 附件列表(可选),内容为附件文件绝对路径
     * @return mixed
     */
    public function sendNormalMail($subject, $content, $to, $from = '', $fromName = '', $templateData = [], $attachments = [])
    {
        // 公共参数
        $data = $this->getCommonParameters();

        // 入参
        $data['subject'] = trim($subject);
        if (strpos($content, '<html>') !== false) {
            $data['html'] = $content;
        } else {
            $data['plain'] = $content;
        }
        $data['from'] = trim($from) === '' ? $this->defaultFrom : $from;
        if (trim($fromName) !== '') {
            $data['fromName'] = trim($fromName);
        }

        $xsmtpapi = [];
        if (!is_array($to)) {
            $to = [$to, ];
        }
        $xsmtpapi['to'] = $to;
        $toCount = is_array($to) ? count($to) : 1;
        if (!empty($templateData)) {
            $xsmtpapi['sub'] = $this->getTemplateData($templateData, $toCount);
        }
        $data['xsmtpapi'] = json_encode($xsmtpapi);
        $files = [];
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $files[] = [
                    'name' => 'attachments',
                    'path' => $attachment,
                ];
            }
        }
        // 请求
        if (!empty($files)) {
            return $this->postWithFile(static::API_MAIL_SEND, $data, $files);
        }
        return $this->post(static::API_MAIL_SEND, $data);
    }

    /**
     * 模板发送
     * @param string $template 邮件模板调用名称
     * @param array|string $to 接收地址(列表)
     * @param string $from 发件人地址
     * @param string $fromName 发件人名称(可选)
     * @param string $subject 邮件标题(可选),默认使用模板标题
     * @param array $templateData 模板数据(可选),key-value形式,key不包含%
     *     如有多个收件地址,相同key的value不同,value可以为数组形式,如: 'key' => ['value1', 'value2']
     *     如有多个收件地址,相同key的value也相同,value同单个收件地址的情况,如: 'key' => 'value'
     * @param array $attachments 附件列表(可选),内容为附件文件绝对路径
     * @return mixed
     */
    public function sendTemplateMail($template, $to, $from = '', $fromName = '', $subject = '', $templateData = [], $attachments = [])
    {
        // 公共参数
        $data = $this->getCommonParameters();

        // 入参
        $data['templateInvokeName'] = $template;
        $data['from'] = trim($from) === '' ? $this->defaultFrom : $from;
        if (trim($fromName) !== '') {
            $data['fromName'] = trim($fromName);
        }
        if (trim($subject) !== '') {
            $data['subject'] = trim($subject);
        }

        $xsmtpapi = [];
        if (!is_array($to)) {
            $to = [$to, ];
        }
        $xsmtpapi['to'] = $to;
        $toCount = is_array($to) ? count($to) : 1;
        if (!empty($templateData)) {
            $xsmtpapi['sub'] = $this->getTemplateData($templateData, $toCount);
        }
        $data['xsmtpapi'] = json_encode($xsmtpapi);
        $files = [];
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $files[] = [
                    'name' => 'attachments',
                    'path' => $attachment,
                ];
            }
        }
        // 请求
        if (!empty($files)) {
            return $this->postWithFile(static::API_MAIL_SEND_TEMPLATE, $data, $files);
        }
        return $this->post(static::API_MAIL_SEND_TEMPLATE, $data);
    }

    /**
     * 拼装公共参数
     * @return array
     */
    public function getCommonParameters()
    {
        $params = [];
        $params['apiUser'] = $this->apiUser;
        $params['apiKey'] = $this->apiKey;
        return $params;
    }

    /**
     * 拼装模板变量参数
     * @param array $templateData 模板参数
     * @param int $emailCount 接收地址个数
     * @return array
     */
    protected function getTemplateData($templateData, $emailCount = 1)
    {
        $ret = [];
        foreach ($templateData as $key => $value) {
            $realKey = '%' . $key . '%';
            if (is_array($value)) {
                $ret[$realKey] = $value;
                continue;
            }
            $ret[$realKey] = array_fill(0, $emailCount, $value);
        }
        return $ret;
    }

    /**
     * post请求
     * @param string $url 接口相对路径
     * @param array $data 接口传参
     * @param array $headers HTTP Header
     * @return mixed
     * @throws GuzzleException
     */
    protected function post($url, $data, $headers = [])
    {
        $request = new Request('POST', $url, $headers);
        $response = $this->apiClient->send(
            $request,
            [
                'form_params' => $data,
            ]
        );
        $result = json_decode($response->getBody(), true);
        return $result;
    }

    /**
     * get请求
     * @param string $url 接口相对路径
     * @param array $data 接口传参
     * @param array $headers HTTP Header
     * @return mixed
     * @throws GuzzleException
     */
    protected function get($url, $data, $headers = [])
    {
        $request = new Request('GET', $url, $headers);
        $response = $this->apiClient->send(
            $request,
            [
                'query' => $data,
            ]
        );
        $result = json_decode($response->getBody(), true);
        return $result;
    }

    /**
     * 带附件post请求
     * @param string $url 接口相对路径
     * @param array $data 接口传参
     * @param array $files 附件列表
     * @param array $headers HTTP Header
     * @return mixed
     * @throws GuzzleException
     */
    protected function postWithFile($url, $data, $files = [], $headers = [])
    {
        $multipart = [];
        foreach ($data as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }
        foreach ($files as $file) {
            $multipart[] = [
                'name' => $file['name'],
                'contents' => fopen($file['path'], 'r'),
            ];
        }
        $request = new Request('POST', $url, $headers);
        $response = $this->apiClient->send(
            $request,
            [
                'multipart' => $multipart,
            ]
        );
        $result = json_decode($response->getBody(), true);
        return $result;
    }

}