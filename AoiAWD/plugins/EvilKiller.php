<?php
namespace aoiawd\plugin;

new EvilKiller(PluginManager::getInstance());

class EvilKiller{
    /** @var PluginManager */
    private $pluginManager;


    public function __construct($manager)
    {
        $this->pluginManager = $manager;
        $this->pluginManager->register('Web', 'processRequest', [$this, 'processWebRequest']);
    }

    public function processWebRequest($data){
        var_dump($data);
        $before = $data;
        $getData = $data['get'];
        $postData = $data['post'];
        $cookieData = $data['cookie'];
        $fileData = $data['file'];
        $headerData = $data['header'];
        $uuid = $data['uuid'];
        if($getData){
            $getData = $this->santinize($getData);
        }
        if($postData){
            $postData = $this->santinize($postData);
        }
        if($cookieData){
            $cookieData = $this->santinize($cookieData);
        }
        if($fileData){
            $fileData = $this->santinize($fileData);
        }
        if($headerData){
            $headerData = $this->santinize($headerData);
        }
        //如果$getData的值被过滤了，那么就把$getData的值放到$after中,用$getData['isFiltered']来判断
        if($getData != []){
            $after['get'] = $getData;
        }
        if($postData != []){
            $after['post'] = $postData;
        }
        if($cookieData != []){
            $after['cookie'] = $cookieData;
        }
        if($fileData != []){
            $after['file'] = $fileData;
        }
        if($headerData != []){
            $after['header'] = $headerData;
        }
        if($after != []){
            $json = json_encode($after);
            $this->pluginManager->getInvoker()->setAlert('EvilKiller', "发现本次Web请求包含恶意字段");
        }
        $after['uuid'] = $uuid;
        return $after;
    }

    private function santinize($data){
        //$data是一个类似$_GET的数组，递归对$data的值进行过滤,将所有的值都过滤一遍，如果有值被过滤了，就讲结果添加到$result中
        $result = [];
        foreach($data as $key => $value){
            if(is_array($value)){
                $temp = $this->santinize($value);
                if($temp != []){
                    $result[$key] = $temp;
                }
            }else{
                $temp = $this->filter($value);
                if($temp){
                    $result[$key] = $temp;
                }
            }
        }
        return $result;
    }

    private function filter($data){
        if($data == "bar"){
            return "shitshit";
        }
        return false;
    }
}