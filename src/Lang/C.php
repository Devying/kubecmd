<?php
namespace KubeCmd\Lang;
use KubeCmd\KubeCtl;
class C {
    private $bin = '';

    private $kubectl;

    private function setBin($bin) {
        $this->bin = $bin;
    }
    public function __construct(KubeCtl $kubectl) {
        $this->kubectl = $kubectl;
        $this->setBin($kubectl->getLangBin("c"));
    }
    public function run($path,$indexFile,$files=array()){
        $compileName = rtrim($indexFile,".c");//�������ļ���
        $outer= $indexFile . ".out";
        $compile_input ="";
        foreach($files as $v){
            if(strrchr($v['filename'],".")==".c"){
                $compile_input.= " ".$path."/".$v['filename'];
            }
        }
        $shell_compile = $this->bin." -o ".$path."/".$compileName." ".$compile_input." 2> ".$path."/".$outer; //��������
        $shell_run = $path."/".$compileName." > ".$path."/".$outer." 2>&1"; //ִ������
        exec ( $this->kubectl->shell." ".$shell_compile,$output,$return );
        if(file_exists($path."/".$compileName)) { //����ɹ�
            exec($this->kubectl->shell." ".$shell_run, $out_run, $return_run);
        }
        return file_get_contents($path."/".$outer);
    }
}