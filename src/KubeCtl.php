<?php
namespace KubeCmd;
use Mockery\CountValidator\Exception;

class KubeCtl {

    const KUBECTL="kubectl";

    public $shell='';

    public $pod='';

    public $containerId='';

    public $lang='';


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

    public function getLang($lang){
        if(!isset($this->langBin[$lang])){
            throw new Exception("Undefined Lang");
        }
        //$class = ucfirst($lang);
        //return new Lang\.$class($this);

        $class = "Lang\\".ucfirst($lang);

        return new $class($this);

//        switch($lang){
//            case "c":
//                $shell = new Lang\C($this);
//                break;
//            case "cpp":
//                $shell = new Lang\Cpp($this);
//                break;
//            case "csharp":
//                $shell = new Lang\Csharp($this);
//                break;
//            case "golang":
//                $shell = new Lang\Golang($this);
//                break;
//            case "java":
//                $shell = new Lang\Java($this);
//                break;
//            case "node":
//                $shell = new Lang\Node($this);
//                break;
//            case "oc":
//                $shell = new Lang\Oc($this);
//                break;
//            case "php":
//                $shell = new Lang\Php($this);
//                break;
//            case "python":
//                $shell = new Lang\Python($this);
//                break;
//            case "ruby":
//                $shell = new Lang\Ruby($this);
//                break;
//            default:
//                throw new Exception("Undefined Lang");
//        }
//        return $shell;
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