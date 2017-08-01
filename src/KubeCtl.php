<?php
namespace KubeCmd;
use Mockery\CountValidator\Exception;

class KubeCtl {

    const KUBECTL="kubectl";

    public $shell='';

    public $pod='';

    public $containerId='';

    public $lang='';

    //define run bin path
    public $langBin=[
        "c"=>"gcc",
        "cpp"=>"g++",
        "csharp"=>[
            "compile"=>"/usr/bin/mcs",
            "run"=>"/usr/bin/mono"
        ],
        "golang"=>"/usr/bin/go",
        "java"=>[
            "compile"=>"javac",
            "run"=>"java"
        ],
        "node"=>"node",
        "oc"=>"clang",
        "php"=>"/data/php/bin/php",
        "python"=>"python",
        "ruby"=>"/usr/local/bin/ruby",
    ];
    public static $instance;
    private function __construct($pod,$namespace='',$containerId=''){
        $this->shell= self::KUBECTL." exec ".$this->pod." ".($containerId?$containerId:"  ").($namespace?" --namespace =".$namespace:"")." ";
    }

    public static function getInstance($pod,$namespace='',$containerId=''){
        if(!self::$instance){
            self::$instance = new self($pod,$namespace,$containerId);
        }
        return self::$instance;
    }
    //return a lang instance
    public function getLang($lang){
        if(!isset($this->langBin[$lang])){
            throw new Exception("Undefined Lang");
        }
        $class = "KubeCmd\\Lang\\".ucfirst($lang);
        return new $class($this);
    }

    public function getLangBin($lang){
        if(!isset($this->langBin[$lang])){
            throw new Exception("Undefined Lang");
        }
        return $this->langBin[$lang];
    }

    public function getLangCompile($lang){
        if(!isset($this->langBin[$lang])){
            throw new Exception("Undefined Lang");
        }
        return $this->langBin[$lang]['compile'];
    }

    public function getLangRun($lang){
        if(!isset($this->langBin[$lang])){
            throw new Exception("Undefined Lang");
        }
        return $this->langBin[$lang]['run'];
    }

    public function writeFile($path,$files=array()){
        if(!$path||empty($files)){
            throw new Exception("Path or Files empty");
        }
        foreach ($files as $value) {
            if(!isset($value['content'])||!isset($value['filename'])||trim($value['filename'])==''){
                throw new Exception("Files filed error");
            }
            if(!is_dir($path)){
                if(!mkdir($path,0777,true)){
                    throw new Exception("create dir failed");
                }
            }
            file_put_contents($path."/".$value['filename'], $value['content'])
        }
    }

    public function runCommand($command,$cwd) {
        $command = $this->shell.$command;
        $descriptorspec = array(
            1 => array(
                'pipe',
                'w'
            ),
            2 => array(
                'pipe',
                'w'
            )
        );
        $pipes = array();
        $resource = proc_open($command, $descriptorspec, $pipes, $cwd);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
        $status = trim(proc_close($resource));
        if ($status && $stderr) {
            return $stderr;
            //return false;
        }
        return $stdout;
    }
}
?>
