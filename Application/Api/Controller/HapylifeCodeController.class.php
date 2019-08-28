<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife美国编码控制器
**/
class HapylifeCodeController extends HomeBaseController{
    public function _initialize(){
        if(time() >= strtotime('2019-09-03 23:59:59')){
            die;
        }
    }
    /**
    * 获取美国编码
    **/ 
    public function UsaCode(){
        $CustomerID = I('post.CustomerID');
        $period = I('post.period',0);
        $business = I('post.business',0);
        $usa = new \Common\UsaApi\Usa();
        $activities = $usa->activities($CustomerID);
        // p($activities);
        if(!$activities['errors']){
            $weekly = $activities['weekly'];
            $monthly = $activities['monthly'];
            if($period){
                $description = $monthly['description'];
                $personalActive = $monthly['personalActive'];
                $resolve_p = explode(' ',$monthly['paidRank']);
                $resolve_t = explode(' ',$monthly['titleRank']);
                $paidRank = substr($resolve_p[0],0,1).substr($resolve_p[1],0,1);
                $titleRank = substr($resolve_t[0],0,1).substr($resolve_t[1],0,1);
                switch($business){
                    case '0':
                        $newTotal = bcadd($monthly['newBinaryUnlimitedLevelsLeft'],$monthly['newBinaryUnlimitedLevelsRight'],0);
                        $activeTotal = bcadd($monthly['activeLeftLegWithAutoPlacement'],$monthly['activeRightLegWithAutoPlacement'],0);
                        $Total = bcadd($monthly['leftLegTotal'],$monthly['rightLegTotal'],0);
                        $volumeTotal = bcadd($monthly['volumeLeft'],$monthly['volumeRight'],0);
                        break;
                    case '1':
                        $newTotal = $monthly['newBinaryUnlimitedLevelsLeft'];
                        $activeTotal = $monthly['activeLeftLegWithAutoPlacement'];
                        $Total = $monthly['leftLegTotal'];
                        $volumeTotal = $monthly['volumeLeft'];
                        break;
                    case '2':
                        $newTotal = $monthly['newBinaryUnlimitedLevelsRight'];
                        $activeTotal = $monthly['activeRightLegWithAutoPlacement'];
                        $Total = $monthly['rightLegTotal'];
                        $volumeTotal = $monthly['volumeRight'];
                        break;
                }
            }else{
                $description = $weekly['description'];
                $personalActive = $weekly['personalActive'];
                $resolve_p = explode(' ',$weekly['paidRank']);
                $resolve_t = explode(' ',$weekly['titleRank']);
                $paidRank = substr($resolve_p[0],0,1).substr($resolve_p[1],0,1);
                $titleRank = substr($resolve_t[0],0,1).substr($resolve_t[1],0,1);
                switch($business){
                    case '0':
                        $newTotal = bcadd($weekly['newBinaryUnlimitedLevelsLeft'],$weekly['newBinaryUnlimitedLevelsRight'],0);
                        $activeTotal = bcadd($weekly['activeLeftLegWithAutoPlacement'],$weekly['activeRightLegWithAutoPlacement'],0);
                        $Total = bcadd($weekly['leftLegTotal'],$weekly['rightLegTotal'],0);
                        $volumeTotal = bcadd($weekly['volumeLeft'],$weekly['volumeRight'],0);
                        break;
                    case '1':
                        $newTotal = $weekly['newBinaryUnlimitedLevelsLeft'];
                        $activeTotal = $weekly['activeLeftLegWithAutoPlacement'];
                        $Total = $weekly['leftLegTotal'];
                        $volumeTotal = $weekly['volumeLeft'];
                        break;
                    case '2':
                        $newTotal = $weekly['newBinaryUnlimitedLevelsRight'];
                        $activeTotal = $weekly['activeRightLegWithAutoPlacement'];
                        $Total = $weekly['rightLegTotal'];
                        $volumeTotal = $weekly['volumeRight'];
                        break;
                }
            }
            $data = array(
                'description' => $description,
                'personalActive' => $personalActive,
                'newTotal' => $newTotal,
                'activeTotal' => $activeTotal,
                'Total' => $Total,
                'volumeTotal' => $volumeTotal,
                'paidRank' => $paidRank,
                'titleRank' => $titleRank,
            );
            $this->ajaxreturn($data);
        }else{
            // 无数据
            $data = array(
                'description' => '无',
                'personalActive' => '无',
                'newTotal' => '无',
                'activeTotal' => '无',
                'Total' => '无',
                'volumeTotal' => '无',
                'paidRank' => '无',
                'titleRank' => '无',
            );
            $this->ajaxreturn($data);
        }
    } 




}