<?php 

!defined('SYSTEM_ACCESS') && exit('Access deny!');

//订单数据处理

class NetPayToOrder extends Common{
	
	/**
	 * 订单以及付款单状态修改
	 * @param array $inv
	 * @param array $pinfo
	 * @param string $getWay，默认：allinpay，其他：yijifu，易极付
	 * @return string
	 */
	public function updateSDKOrderStatus($inv = array()){
		$extarr = explode("_",$inv['ext1']);
		$clientid  = $extarr[1];
		$companyid = $extarr[0];
		$ucinfo = $this->db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$companyid." limit 0,1");
			
		if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;
		if(!empty($inv['ext2'])){
			$sql_o  = " update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderSDK='". $inv['tradeNo']."' where OrderCompany=".$companyid." and instr('".$inv['ext2'].",',OrderSN) > 0 ";
			$this->db->query($sql_o);
		}
	}
	
	/**
	 * 锁定订单状态
	 * @param array $inv
	 * @return string
	 */
	public function lockOrderStatus($inv = array()){
		
		$extarr = explode("_",$inv['ext1']);
		$clientid  = $extarr[1];
		$companyid = $extarr[0];
		$ucinfo = $this->db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$companyid." limit 0,1");
			
		if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;
		if(!empty($inv['ext2'])){
			$sql_insert = "insert into ".DATABASEU.DATATABLE."_order_payno_check(payno) values('".$inv['orderNo']."') ";
			$status_insert	= $this->db->query($sql_insert);
			
			if($this->db->rows_affected){
				$sql_o  = " update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=4 where OrderCompany=".$companyid." and instr('".$inv['ext2'].",',OrderSN) > 0 and OrderPayStatus!=2";
				$this->db->query($sql_o);
			}
		}
	}
	
	/**
	 * 插入check表，避免出错
	 * @param array $inv
	 * @return string
	 */
	public function insertPaynoCheck($inv = array()){
		$sql_insert = "insert into ".DATABASEU.DATATABLE."_order_payno_check(payno) values('".$inv['orderNo']."') ";
		$status_insert	= $this->db->query($sql_insert);
	}
	/**
	 * 解锁订单状态
	 * @param array $inv
	 * @return string
	 */
	public function unLockOrderStatus($inv = array()){
		$extarr = explode("_",$inv['ext1']);
		$clientid  = $extarr[1];
		$companyid = $extarr[0];
		$ucinfo = $this->db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$companyid." limit 0,1");
			
		if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;
		if(!empty($inv['ext2'])){
			$sql_o  = "select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus from ".$datacbase.".".DATATABLE."_order_orderinfo where OrderCompany=".$companyid." and instr('".$inv['ext2'].",',OrderSN) > 0 order by OrderID asc ";
			$olist  =  $this->db->get_results($sql_o);
			if(!empty($olist))
			{
				foreach($olist as $osv)
				{
					if((round($osv['OrderIntegral'],2) > 0)&&(round($osv['OrderIntegral'],2)<(round($osv['OrderTotal'],2)))){
						$sql_o  = " update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=3 where OrderCompany=".$companyid." and OrderPayStatus!=2 and OrderID=".$osv['OrderID'];
						$this->db->query($sql_o);
					}else{
						$sql_o  = " update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=0 where OrderCompany=".$companyid." and OrderPayStatus!=2 and OrderID=".$osv['OrderID'];
						$this->db->query($sql_o);
					}
				}
			}
		}
	}
	
	/**
	 * 订单以及付款单状态修改
	 * @param array $inv
	 * @param array $pinfo
	 * @param string $getWay，默认：allinpay，其他：yijifu，易极付
	 * @return string
	 */
	public function updateOrderStatus($inv = array(), $pinfo = array(), $getWay = 'allinpay'){

		if(empty($pinfo['PayResult']))
		{
			
			$log    = KLogger::instance(LOG_PATH, KLogger::INFO);
			 
			$extarr = explode("_",$inv['ext1']);
			$clientid  = $extarr[1];
			$companyid = $extarr[0];
// 			$inv['payAmount'] = $inv['payAmount'] / 100;
			 $sqlin = "update 
							".DATABASEU.DATATABLE."_order_netpay set 
							PayTradeNO='".$inv['PayTradeNO']."',
							PayDateTime='".$inv['PayDateTime']."',
							PayResult='".$inv['PayResult']."',
							ErrorCode='".$inv['ErrorCode']."',
							ReturnDateTime='".$inv['ReturnDateTime']."' 
						where 
							PayID='".$pinfo['PayID']."' 
							and CompanyID=".$pinfo['CompanyID']." 
							and ClientID=".$pinfo['ClientID']." 
						limit 1";
			$log->logInfo('receive-sqlin', $sqlin);
			
			$status = $this->db->query($sqlin);
			
			if($status)
			{
				$ucinfo = $this->db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$companyid." limit 0,1");

			
				if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;
				if((!empty($inv['ext2']) && strpos($inv['ext2'],',')) || empty($inv['ext2'])){
					$FinanceOrderID = 0;
				}else{
					$oinfo = $this->db->get_row("select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderPayStatus,OrderStatus from ".$datacbase.".".DATATABLE."_order_orderinfo where OrderSN='".$inv['ext2']."' and OrderCompany=".$companyid." limit 0,1");
					$FinanceOrderID = $oinfo['OrderID'];
				}
				
				//数据正确，自动确认付款单[2]；反之，待确认[0]
				$finaceFlag = $inv['PayResult'] ? 2 : 0;
					
				//查看是否已写入了付款单
				$csql = "select FinanceID from ".$datacbase.".".DATATABLE."_order_finance where FinanceCompany=".$companyid." and FinanceClient=".$clientid." and FinancePaysn='".$inv['orderNo']."'";
				$cresult = $this->db->get_row($csql);
				
				if(empty($cresult['FinanceID'])){//未写入数据
					//tubo 增加判断易极付传过来两个相同流水号的处理情况  2016-01-18
					$sql_insert = "insert into ".DATABASEU.DATATABLE."_order_payno(payno) values('".$inv['orderNo']."') ";
					$status_insert	= $this->db->query($sql_insert);
					if($this->db->rows_affected){
					    //此处为原处理代码
						$sql_l  = "insert into ".$datacbase.".".DATATABLE."_order_finance(FinanceCompany,FinanceClient,FinanceOrderID,FinanceOrder,FinanceAccounts,FinanceTotal,FinancePicture,FinanceAbout,FinanceToDate,FinanceUpDate,FinanceDate,FinanceUser,FinanceFlag,FinancePaysn,FinanceType,FinanceFrom) values(".$companyid.", ".$clientid.", ".$FinanceOrderID.",'".$inv['ext2']."', 0, '".$inv['payAmount']."', '', '快捷支付-".$inv['resultMessage']."', '".date("Y-m-d")."', ".time().", ".time().",'', ".$finaceFlag.",'".$inv['orderNo']."','O','".$getWay."')";
						$log->logInfo('sqlin_finance', $sql_l);
		
						$status	= $this->db->query($sql_l);
						//原代码end
					}else{
						exit;
					}
					//end 易极付修改 2016-01-18
					
				}else{//已写入付款单，进行更新
					$usql = "update ".$datacbase.".".DATATABLE."_order_finance set FinanceFlag=".$finaceFlag." where FinanceID=".$cresult['FinanceID'];
					$log->logInfo('sqlupdate_finance', $usql);
					$this->db->query($usql);
					
				}
				
				if(!empty($inv['ext2'])){
					$sql_o  = "select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus from ".$datacbase.".".DATATABLE."_order_orderinfo where OrderCompany=".$companyid." and instr('".$inv['ext2'].",',OrderSN) > 0 order by OrderID asc ";
					$olist  =  $this->db->get_results($sql_o);
					if(!empty($olist))
					{
						$chatotal = $inv['payAmount'];
						foreach($olist as $osv)
						{
							if(!empty($osv['OrderTotal']))
							{
								$chatotal = round($chatotal - $osv['OrderTotal'] + $osv['OrderIntegral'],2);
								if(!$inv['PayResult']){//付款状态未确定
									$upsql = "update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=4 where OrderID = '".$osv['OrderID']."' limit 1";
									$isup  = $this->db->query($upsql);
									$log->logInfo('upsql', $upsql);
								}elseif($chatotal >= 0){
									$upsql = "update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=2, OrderPayType=9, OrderIntegral='".$osv['OrderTotal']."' where OrderID = '".$osv['OrderID']."' limit 1";
									$isup  = $this->db->query($upsql);
									$log->logInfo('upsql', $upsql);
								}else{
									$uptotal = $chatotal + $osv['OrderTotal'];
									$upsql = "update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=3, OrderIntegral='".$uptotal."' where OrderID = '".$osv['OrderID']."' limit 1";
									$isup  = $this->db->query($upsql);
									$log->logInfo('upsql', $upsql);
									break;
								}
							}
							$lastosv = $osv['OrderSN'];
						}
					}
				}
				
			/****************************************** 发送短信 ******************************************************************/
			
			$csql = "select FinanceFlag from ".$datacbase.".".DATATABLE."_order_finance where FinanceCompany=".$companyid." and FinanceClient=".$clientid." and FinancePaysn='".$inv['orderNo']."'";
			$cresult = $this->db->get_row($csql);
			
			//没有更新成功则不发短信
			if($cresult['FinanceFlag'] != 2) return true;
			
			$ucinfo = $this->db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$companyid." limit 0,1");
			if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;
			$upsql =  "select 
							ClientName,ClientCompanyName,ClientTrueName 
						from 
							".$datacbase.".".DATATABLE."_order_client 
						where  
							ClientID=".$clientid." 
							and ClientCompany=".$companyid." 
							and ClientFlag=0 
						limit 0,1";	
			
			$clientinfo = $this->db->get_row($upsql);
			$message = "【".$ucinfo['CompanySigned']."】经销商:".$clientinfo['ClientTrueName']."-".$clientinfo['ClientCompanyName']."于".date("Y-m-d")."通过\"快捷支付\"支付一笔金额为: ¥ ".number_format($inv['payAmount'], 2, '.', '')."元的款项,请注意查收。退订回复TD";
			//异步通知时，需要设置该项
			$smsSql = "select CS_SmsNumber from ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$companyid." limit 1";
			$csinfo = $this->db->get_row($smsSql);
//			$csinfo['CS_SmsNumber'] = 10;
			$_SESSION['ucc']['SmsNumber']	= $csinfo['CS_SmsNumber'];
			$_SESSION['cc']['cid']			= $clientid;
			$_SESSION['cc']['cusername']	= $clientinfo['ClientName'];
			$_SESSION['cc']['ccompany']		= $companyid;
			
			//支付网关类型[todo: 如果以后还增加支付网关的话，这里需要根据[$getWay]动态设置]
//			$NetGetWay	= new NetGetWay();
//			$netInfo	= $NetGetWay->showGetway('yijifu', $companyid, '', true);
// 			sms::get_setsms("3",$message);
			/****************************************** 发送短信 ******************************************************************/
			
			}
		}
	}//END updateOrderStatus
	
	
	
}