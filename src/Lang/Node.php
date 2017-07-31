<?php
namespace KubeCmd\Lang;
use KubeCmd\KubeCtl;
class Node {
    private $bin = '';
    private $kubectl;
    private function setBin($bin) {
        $this->bin = $bin;
    }
    public function __construct(KubeCtl $kubectl) {
        $this->kubectl = $kubectl;
        $this->setBin($kubectl->getLangBin("node"));
    }

    public function run($path,$indexFile,$files=array()){
        $out = $indexFile.".out";
        $shell = $this->bin." ".$path."/".$indexFile ." > ".$path."/".$out." 2>&1";
        exec ( $this->kubectl->shell." ".$shell,$output,$return );
        return file_get_contents($out);
    }
}