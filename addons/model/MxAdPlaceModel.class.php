<?php
/**
 * Created by JetBrains PhpStorm.
 * User: caipeichao
 * Date: 8/27/13
 * Time: 6:52 PM
 * To change this template use File | Settings | File Templates.
 */

class MxAdPlaceModel extends model {
    protected $tableName = 'adspace_place';
    protected $_error;

    public function addPlace($name, $description) {
        $res = $this->add(array('name' => $name, 'description' => $description));
        return (boolean) $res;
    }

    public function getAllPlaces() {
        $result = $this->table(C('DB_PREFIX').$this->tableName)->findAll();
        return $result;
    }

    public function isPlaceExist($place_name) {
        $list = $this->where(array('name'=>$place_name))->count();
        $result = (boolean)$list;
        return $result;
    }
}