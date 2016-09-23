<?php
/**
 * PHP File
 * User: chocoboxxf
 * Date: 16/9/23
 */
namespace chocoboxxf\SendCloud\Tests;

use Yii;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string 测试用发件人地址
     */
    public $from = 'admin@company.com';
    /**
     * @var string 测试用发件人名称
     */
    public $fromName = 'Admin';
    /**
     * @var string 测试用收件人地址
     */
    public $to = ['user@company.com'];
    /**
     * @var \chocoboxxf\SendCloud\SendCloud
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();
        $this->from = isset($_ENV['TEST_FROM']) ? $_ENV['TEST_FROM'] : $this->from;
        $this->fromName = isset($_ENV['TEST_FROM_NAME']) ? $_ENV['TEST_FROM_NAME'] : $this->fromName;
        $this->to = isset($_ENV['TEST_TO']) ? $_ENV['TEST_TO'] : $this->to;
        $this->client = Yii::createObject([
            'class' => 'chocoboxxf\SendCloud\SendCloud',
            'apiUser' => isset($_ENV['API_USER']) ? $_ENV['API_USER'] : 'API_USER',
            'apiKey' => isset($_ENV['API_KEY']) ? $_ENV['API_KEY'] : 'API_KEY',
        ]);
    }
}
