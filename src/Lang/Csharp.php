<?php
namespace KubeCmd\Lang;
use KubeCmd\KubeCtl;
class Csharp {
    private $comBin='';
    private $runBin='';
    private $kubectl;

    private function setComBin($bin) {
        $this->comBin = $bin;
    }
    private function setRunBin($bin) {
        $this->runBin = $bin;
    }
    public function __construct(KubeCtl $kubectl) {
        $this->kubectl = $kubectl;
        $this->setComBin($kubectl->getLangCompile("csharp"));
        $this->setRunBin($kubectl->getLangRun("csharp"));
    }

    public function run($path,$indexFile,$files=array()){
        $compileName = rtrim($indexFile,".cs").".exe";//编译后的文件名
        $outer= $indexFile . ".out";
        $compile_input ="";
        foreach($files as $v){
            if(strrchr($v['filename'],".")==".cs"){
                $compile_input.= " ".$path."/".$v['filename'];
            }
        }
        $compile_input=trim($compile_input);
        $shell_compile = "export LANG=en_US.UTF-8;".$this->comBin." ".$compile_input." 2> ".$path."/".$outer; //编译命令
        $shell_run = "export LANG=en_US.UTF-8;".$this->runBin." ".$path."/".$compileName." > ".$path."/".$outer." 2>&1"; //执行命令
        exec ( $this->kubectl->shell." ".$shell_compile,$output,$return );
        if(file_exists($path."/".$compileName)) { //编译成功
            exec($this->kubectl->shell." ".$shell_run, $out_run, $return_run);
        }
        return file_get_contents($path."/".$outer);
    }
}