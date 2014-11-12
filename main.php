<?php


class ConsistentHash {

  private $indexes = []; //array of hash keys
  private $circle = [];  //sorted hash
  private $map = [];     //hash

  public function __construct($number_of_nodes, $number_of_virtual_nodes) {
    foreach(range(1, $number_of_nodes) as $i) {
      $node = "node" . $i;
      $key = $this->hashFunc($node);
      $this->circle[$key] = $node;
      $this->map[$node] = [];

      // virtual node
      foreach(range(0, $number_of_virtual_nodes) as $number) {
        $v_node = $node . "_" . $number;
        $key = $this->hashFunc($v_node);
        $this->circle[$key] = $node;
        $this->map[$node] = [];
      }
    }

    ksort($this->circle);
    $this->indexes = array_keys($this->circle);
  }

  private function hashFunc($node) {
    $hash = md5($node);
    //return base_convert($hash, 16, 10);
    return hexdec(substr($hash, 0,8));
  }


  public function add($value) {
    $key = $this->hashFunc($value);
    $node = $this->searchNode($key);
    $this->map[$node][] = $value;
  }

  private function searchNode($key) {
    $node_key = $this->searchNodeKey($key);
    return $this->circle[$node_key];
  }
  private function searchNodeKey($key) {
    $node = '';
    for($i = 1; $i < count($this->indexes); $i++) {
      if ($this->indexes[$i-1] < $key && $key <= $this->indexes[$i]) {
        return $this->indexes[$i];
      }
    }
    return $this->indexes[0];
  }

  public function getMap() {
    return $this->map;
  }
}


$c = new ConsistentHash(3, 100);
    foreach(range('A', 'Z') as $value) {
      $c->add($value);
    }
$circle = $c->getMap();

echo var_export($circle, 1);

