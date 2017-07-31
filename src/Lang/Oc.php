<?php
namespace KubeCmd\Lang;
use KubeCmd\KubeCtl;
class Oc {
    private $bin = '';
    private $kubectl;
    private function setBin($bin) {
        $this->bin = $bin;
    }
    public function __construct(KubeCtl $kubectl) {
        $this->kubectl = $kubectl;
        $this->setBin($kubectl->getLangBin("oc"));
    }

    public function run($path,$indexFile,$files=array()){
        $compileName = "main";//�������ļ���
        $outer= $indexFile . ".out";
        $shell_compile = $this->bin." `gnustep-config --objc-flags` -fobjc-arc -fobjc-nonfragile-abi -lobjc -lgnustep-base `gnustep-config --objc-libs` ".$path."/*.m -o main 2>".$outer;//��������
        $shell_run = $path."/".$compileName." > ".$path."/".$outer." 2>&1"; //ִ������
        exec ( $this->kubectl->shell." ".$shell_compile,$output,$return );
        if(file_exists($path."/".$compileName)) { //����ɹ�
            exec($this->kubectl->shell." ".$shell_run, $out_run, $return_run);
        }
        return file_get_contents($path."/".$outer);
    }
}