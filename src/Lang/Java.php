<?php
namespace KubeCmd\Lang;
use KubeCmd\KubeCtl;
class Java {
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
        $this->setComBin($kubectl->getLangCompile("java"));
        $this->setRunBin($kubectl->getLangRun("java"));
    }

    public function run($path,$indexFile,$files=array()){
        $compileName = rtrim($indexFile,".java");//�������ļ���
        $outer= $indexFile . ".out";
        $shell_compile = $this->comBin." -encoding UTF-8 ".$path."/".$indexFile." 2> ".$path."/".$outer; //��������
        $shell_run = $this->runBin." -Dfile.encoding=UTF-8 -cp ".$path." ".$compileName." > ".$path."/".$outer." 2>&1"; //ִ������
        exec ( $this->kubectl->shell." ".$shell_compile,$output,$return );
        if(file_exists($path."/".$compileName.".class")) { //����ɹ�
            exec($this->kubectl->shell." ".$shell_run, $out_run, $return_run);
        }
        return file_get_contents($path."/".$outer);
    }
}