<?php
namespace Think\Upload\Driver;
use \Aliyun\OSS\OSSClient;
require_once dirname(__FILE__).'/Aliyun/aliyun.php';

class Aliyun{
    private $config = array(
        'AccessKeyId' => '', //OSS用户
        'AccessKeySecret' => '', //OSS密码
        'domain'        =>'', 
        'Bucket'   => '', //空间名称
        'Endpoint'  => '', //超时时间
    );
    private $error      =   '';
    public function __construct($config){
        /* 默认配置 */
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 检测上传根目录(OSS上传时支持自动创建目录，直接返回)
     * @param string $rootpath   根目录
     * @return boolean true-检测通过，false-检测失败
     */
    public function checkRootPath($rootpath){
        /* 设置根目录 */
        $this->rootPath = trim($rootpath, './') . '/';
        return true;
    }

    /**
     * 检测上传目录(OSS上传时支持自动创建目录，直接返回)
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkSavePath($savepath){
        return true;
    }

    /**
     * 创建文件夹 (OSS上传时支持自动创建目录，直接返回)
     * @param  string $savepath 目录名称
     * @return boolean          true-创建成功，false-创建失败
     */
    public function mkdir($savepath){
        return true;
    }

    /**
     * 保存指定文件
     * @param  array   $file    保存的文件信息
     * @param  boolean $replace 同名文件是否覆盖
     * @return boolean          保存状态，true-成功，false-失败
     */
    public function save(&$file,$replace=true) {
        $key = $file['savepath'] . $file['savename'];
        $content = fopen( $file['tmp_name'],'r');
        $size = $file['size'];
        $client = $this->client();
        $save = $client->putObject(array(
            'Bucket'    => $this->config['Bucket'],
            'Key'       => $key,
            'Content'   => $content,
            'ContentLength'=> $size,
            ));
        if ($save) {
            $file['url']='http://'.$this->config['domain'].'/'.$key;
            return $this->config['domain'].$key;
        }else{
            return false;
        }
    }
    public function getError(){
        return $this->client->errorStr;
    }

    //创建client对象
    function client(){
        $client = OSSClient::factory(array(
            'Endpoint' => $this->config['Endpoint'],
            'AccessKeyId' => $this->config['AccessKeyId'],
            'AccessKeySecret' => $this->config['AccessKeySecret'],
        ));
        return $client;
    }
}