<?php
/**
 * Created by PhpStorm.
 * User: 建国
 * Date: 2015/3/30
 * Time: 10:14
 */

class ModuleObject extends MasterObject {
    function ModuleObject($config) {
        $this->MasterObject ( $config );
        $runCode = Load::moduleCode ( $this );
        $this->$runCode ();
    }

    function main(){
        $articles = logic('articletype')->get_list();
        include handler('template')->file('@admin/articletype');
    }

    function create(){
        $this->CheckAdminPrivs('articletype');
        $article = array(
            'writer' => MEMBER_NAME
        );
        include handler('template')->file('@admin/articletype_eidt');
    }

    function modify(){
        $this->CheckAdminPrivs('articletype');
        $id = get('id', 'int');
        $article = logic('articletype')->get_one($id);
        include handler('template')->file('@admin/articletype_eidt');
    }

    public function delete()
    {
        $this->CheckAdminPrivs('articletype');
        $id = get('id', 'int');
        logic('articletype')->delete($id);
        $this->Messager('删除成功！', '?mod=articletype');
    }

    function save(){
        $this->CheckAdminPrivs('articletype');
        extract ( $this->Post );
        $rebate = array (
            'typename'=>$typename,
            'status' => $status
        );

        if ($typename)
        {
            $id = post('id', 'int');
            if ($id)
            {
                logic('articletype')->update($id,$rebate);
            }
            else
            {
                logic('articletype')->create($rebate);
            }
            $this->Messager('保存成功！', '?mod=articletype');
        }
        else
        {
            $this->Messager('文章分类名称不能为空！', -1);
        }
    }
}
