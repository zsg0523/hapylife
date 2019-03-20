<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 批量修改发货状态控制器
 */
class BatchReceiptController extends AdminBaseController{
	/**
	* 通过读取excel文件
	* 实现批量修改
	**/ 
	public function UpdateReceipt(){
		$upload = post_upload();
	 	// 文件名称
		$file  = '.'.$upload['name'];
		$arr  = import_excel($file);
		foreach($arr as $key=>$value){
	 		if($key!=1 && trim($value[A]) == 'HPL'){
	 			$data[] = trim($value[C]);
	 		}
	 	}
	 	$json = json_encode($data);
	 	$log = addReceipt($json);
        M('Receipt')->startTrans();   // 开启事务
	 	$trans_result = true;
        try {   // 异常处理
            // 更新实施
            foreach($data as $key=>$value){
            	$result = M('Receipt')->where(array('ir_receiptnum'=>$value))->setfield('ir_status',3);
	 			if($result){
	 				M('Receipt')->where(array('ir_receiptnum'=>$value))->setfield('send_time',time());
	 			}else{
	 				E("错误信息");
	 			}
	 		}
        } catch (\Exception $e) {
            $trans_result = false;
        }

        if ($trans_result === false) {
            M('Receipt')->rollback();
            // 更新失败
            $this->error('修改失败',$_SERVER['HTTP_REFERER']);
        } else {
            M('Receipt')->commit();
            // 更新成功
            $this->success('修改成功',$_SERVER['HTTP_REFERER']);
        }
	}
}