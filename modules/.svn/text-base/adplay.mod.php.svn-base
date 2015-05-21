<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author wzn
 * @package php
 * @name adplay.mod.php
 * @date 2014-09-17
 */
 




class ModuleObject extends MasterObject
{
    private $uid = 0;
    private $sid = 0;
    
    function ModuleObject( $config )
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    public function main()
    {
        $state = get('state', 'int');
        $sort = get('sort');
        if( MEMBER_ID && user()->get('phone')){
        	$cityone = $this->attr(user()->get('phone'));
        }
        $cityid = $cityone ? $cityone['cityid'] : 0 ;
        
        $list = logic('adplay')->GetList_AD_ByUser(user()->get('id'), $state, $sort,$cityid);
        $this->Title = '玩广告';
        include handler('template')->file('adplay');
    }
    public function view()
    {
        $id = get('id', 'int');
        $info = logic('adplay')->GetOne_AD($id);
        $this->Title = '玩广告';
        $qinfo = logic('adplay')->GetOne_Q($info['qid']);
        $ainfoes = logic('adplay')->GetList_A(' and qid='.$info['qid']);
        $nextAd = logic('adplay')->GetNext_AD($id);
        $anslog=logic('adplay')->GetOne_AD_Log($id,user()->get('id'))==null;
        if($nextAd==null)
            $nextAd='0';
        else
            $nextAd=$nextAd['id'];
        
        include handler('template')->file('adplay_view');
    }
    
    /**
     * 归属地查询返回城市
     * */
    function attr($phone){
    	$attr = attribution($phone);
    	if( $attr ){
    		return logic ( 'misc' )->cityOne( trim($attr['city']) );
    	}
    	return null ;
    }
    
    public function answer()
    {
        $id = get('id', 'int');
        $aid = get('aid', 'int');   
          
    	$info = logic('adplay')->GetOne_AD($id);
    	$info || exit();
    	
        if(intval($info['times_played'])>=intval($info['times']))
            exit('5');
        
        if( MEMBER_ID < 1){
        	exit('1');
        }else{
        	$phone=user()->get('phone');
        	$phone || exit('2');
        }
        
        $cityone = $this->attr(user()->get('phone'));
        $cityid = $cityone ? $cityone['cityid'] : 0 ;
        
        if($info['cityid']!=$cityid)
        	exit('4');
        
        //重复回答判断
        $anslog=logic('adplay')->GetOne_AD_Log($id,user()->get('id'));
        if($anslog!=null)
        {
            exit('6');
        }
        $anserror=logic('adplay')->getErrorCount(array('aid'=>$id,'userid'=>user()->get('id')));
        if($info['everybody_times'] && ($anserror['errorcount'] >= $info['everybody_times'])){
        	exit('3');
        }
        //答案判断
        $ainfo=logic('adplay')->GetOne_A($aid);
        if($ainfo==null||$ainfo['isright']=='0')
        {
        	logic('adplay')->addError(array('aid'=>$id,'userid'=>user()->get('id')));
            exit('wrong answer');
        }
        //数据处理
        $adlid = logic('adplay')->Play_AD($id,user()->get('id'));
        
        $adlid || exit('0');
        exit('ok');
    }
}
?>