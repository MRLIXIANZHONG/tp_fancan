<?php

/**
 * Created by PhpStorm.
 * User: 吕卫萌
 * Date: 2019/10/30/030
 * Time: 15:46
 */
namespace app\home\model;



class StorageTable extends Table
{

    /**
     * 新增成功执行计数存储过程 Post_SendCount_U
     * @return int
     */
    public function exePostSendCountU(int $typeId){
        $this->getWriteExecute("Post_SendCount_U ".$this->site_id.",'".session("username")."',$typeId");
    }
    /**
     * 新增成功执行日志存储过程 add_post_action_log
     * @return int
     */
    public function exeAddLog(int $tabCls, int $tempId , string $tableName){
        $this->getWriteExecute("add_post_action_log ".$this->site_id.",'".session("username")."',0,".$tabCls.",'".$tableName."',".$tempId.",1");
    }

    /**
     * 放置标题被刷存储过程 PH_Post_ReleaseCheck_V2
     * @return int
     */
    public function exeReleaseCheck(string $title , string $titleStr, string $tableName, string $tel, string $agent, int $outCode = 0, string $msg='', int $fromType=0){
        //如果要指定变量类型，变量前边必须加&
        $result = $this->getCountExecute('exec PH_Post_ReleaseCheck_V2 ?,?,?,?,?,?,?,?,?,?,?', [
            $this->site_id,
            session("username"),
            getIP(),
            $title,
            $titleStr,
            $tableName,
            [&$outCode, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 4000],
            [&$msg, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 4000],
            $fromType,
            $tel,
            $agent,
        ]);
        $data['code'] = $outCode;
        $data['msg']  = $msg;
        $data['data'] = $result;
        return $data;
    }
    /**
     * 生活服务存储过程 PH_Post_LiveInfo_IUD
     * @return int
     */
    public function exeLiveInfoIUD(array $data){
        $outCode = 0;
        //如果要指定变量类型，变量前边必须加&
        $result = $this->getWriteQuery('exec PH_Post_LiveInfo_IUD ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
            $data['Infokind'],
            $this->site_id,
            session("username"),
            $data['Title'],
            $data['sh_CompName'],
            $data['linkman'],
            $data['tel'],
            $data['email'],
            $data['qq'],
            $data['info'],
            $data['infoNum'],
            '',
            $data['ccoochk'],
            $data['Infokind'],
            $data['ClassId'],
            0,
            0,
            0,
            0,
            0,
            $data['source'],
            '',
            $data['areaId'],
            $data['address'],
            0,
            0,
            0,
            0,
            '',
            '',
            getIP(),
            0,
            $data['pic'],
            1,
            0,
            0,
            0,
            '',
            0,
            [&$outCode, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 4000],
            $data['isdel']
        ]);

        return $outCode;
    }

    /**
     * 二手信息存储过程 PH_Post_EsInfo_v2_IUD (暂时没用)
     * @return int
     */
    public function exeEsInfoIUD(array $data){
        $outCode = 0;
        //如果要指定变量类型，变量前边必须加&
        $result = $this->getWriteQuery('exec PH_Post_EsInfo_v2_IUD ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
            0,
            $this->site_id,
            session("username"),
            $data['IsSource'],
            $data['Title'],
            $data['Price'],
            $data['linkman'],
            $data['tel'],
            $data['email'],
            $data['qq'],
            $data['info'],
            $data['HtmlArea'],
            $data['IsExchange'],
            $data['HtmlExchange'],
            $data['infoNum'],
            '',
            $data['Ccoochk'],
            $data['ChannelId'],
            $data['ClassId'],
            $data['oTherId'],
            $data['smallclassid'],
            $data['FourthId'],
            getIP(),
            0,
            $data['xinghao'],
            $data['buyYear'],
            $data['areaid'],
            $data['fapiao'],
            $data['xuninum'],
            $data['qinglvnum'],
            $data['CPUpinpai'],
            $data['CPUheshu'],
            $data['neicun'],
            $data['yingpan'],
            $data['pingmuchicun'],
            $data['xianka'],
            $data['rongji'],
            1,
            $data['pic'],
            0,
            [&$outCode, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 4000]
        ]);

        return $outCode;
    }
    /**
     * 获取用户未读消息数 usersms_noreadsum
     * @return int
     */
    public function exeUserNoRead(string $userName , int $siteId, string $strcate){
        $result = $this->getSmsWriteQuery('exec usersms_noreadsum ?,?,?', [
            $userName,
            $siteId,
            $strcate,
        ]);
        return $result[0][0];
    }
}