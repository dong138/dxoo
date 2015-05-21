<?php
/**
 * Created by PhpStorm.
 * User: 建国
 * Date: 2015/3/30
 * Time: 11:08
 */

class ArticletypeLogic {
    public function get_list($limit = 0, $filter = 1)
    {
        if ($limit) {
            $sql = dbc(DBCMax)->select('articletype')->where($filter)->order('id.desc')->limit($limit);
        }else{
            $sql = dbc(DBCMax)->select('articletype')->where($filter)->order('id.desc');
        }
        $sql = dbc(DBCMax)->sql($sql);
        if (!$limit) $sql = page_moyo($sql);
        $results = dbc(DBCMax)->query($sql)->done();
        return $results;
    }

    public function get_one($id)
    {
        return dbc(DBCMax)->select('articletype')->where(array('id' => $id))->limit(1)->done();
    }
    public function Update_Article($id, $data)
    {
        dbc()->SetTable(table('articletype'));
        $id = dbc()->Update($data, 'id = '.$id);
        return $id;
    }
    public function create($rebate = array()) {
        return dbc(DBCMax)->insert('articletype')->data($rebate)->done();
    }

    public function update($id,$rebate = array()) {
        extract ( $rebate );
        return dbc(DBCMax)->update('articletype')->data($rebate)->where(array('id' => $id))->done();
    }

    public function delete($id)
    {
        return dbc(DBCMax)->delete('articletype')->where(array('id' => $id))->done();
    }
}