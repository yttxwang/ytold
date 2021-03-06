<?php
include_once ("header.php");
$menu_flag = "manager";
$company_id = $in['ID'];
if(!$company_id) {
    exit('非法操作!');
}
$cinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyID=".intval($in['ID'])." limit 0,1");

$data_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_data WHERE CompanyID={$company_id} LIMIT 1");
$cs_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company={$company_id} LIMIT 1");

?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo SITE_NAME;?> - 管理平台</title>
        <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
        <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

        <script src="../scripts/jquery.min.js" type="text/javascript"></script>
        <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

        <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
        <script type="text/javascript">
            $(function() {
                $("#CS_BeginDate").datepicker();
                $("#CS_EndDate").datepicker();
                $("#CS_UpDate").datepicker();
            });
        </script>
        <style type="text/css">
	.three-silds-box{
		padding-left: 0px;
		display: block;
	}
	.three-silds-box li{
		border: 1px solid #dbdbdb;
		float:left;
		margin: 2px;
	}
	.three-silds-box li img{
		width: 200px;
		height: 150px;
		margin: 2px;
		cursor: pointer;
	}
	.approve-notice{
		float:none;
		clear:both;
		color:#595959;
	}
</style>
    </head>

    <body>
    <?php include_once ("top.php");?>
    <?php include_once ("inc/son_menu_bar.php");?>

    <div id="bodycontent">
        <div class="lineblank"></div>
        <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">

            <div id="searchline">
                <div class="leftdiv width300">
                    <div class="locationl"><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250; <a href="javascript:;">审核资料</a></div>
                </div>

                <div class="rightdiv sublink" style="padding-right:20px;">
                    <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="通过" onclick="do_check_company('T');" />
                    <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="不通过" onclick="do_check_company('F');" />
                    <input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
                </div>

            </div>


            <div class="line2"></div>
            <div class="bline" >
                <fieldset title="“*”为必填项！" class="fieldsetstyle">
                    <legend>属性资料</legend>
                    <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">

                        <tr>
                            <td width="16%" bgcolor="#F0F0F0"><div align="right">所属地区：</div></td>
                            <td width="55%">
                                <label>
                                      <select name="data_CompanyArea" id="data_CompanyArea" class="select2">
                                        <option value="0">⊙ 请选择客户所在地区</option>
                                        <? 
                    					$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city ORDER BY AreaParent ASC, AreaID ASC");
                    					echo ShowTreeMenu($sortarr,0,$cinfo['CompanyArea'],1);
                    					?>
                                      </select>
                                      <span class="red">*</span>
                               </label>
                            </td>
                            <td width="29%"></td>
                        </tr>
                        <tr>
                            <td width="16%" bgcolor="#F0F0F0"><div align="right">所属行业：</div></td>
                            <td>
                                  <select name="data_CompanyIndustry" id="data_CompanyIndustry" class="select2">
                                    <option value="0">⊙ 请选择客户所属行业</option>
                                    <? 
                					$industryarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry  ORDER BY IndustryID ASC ");
                					foreach($industryarr as $ivar)
                					{
                						if($cinfo['CompanyIndustry'] == $ivar['IndustryID'])
                						{
                							echo '<option value="'.$ivar['IndustryID'].'" selected="selected">┠-'.$ivar['IndustryName'].'</option>';
                						}else{
                							echo '<option value="'.$ivar['IndustryID'].'">┠-'.$ivar['IndustryName'].'</option>';
                						}
                					}
                					?>
                                  </select>
                                  <span class="red">*</span>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </fieldset>
                <br style="clear:both;" />
                <fieldset class="fieldsetstyle">
                    <legend>认证信息</legend>
                    <div >
                        <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                        	<tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                   <label> 
                                        <input type="text" name="BusinessName" id="BusinessName" value="<?php echo $data_info['BusinessName']; ?>" />
                                        <span class="red">*</span>
                                   </label>
                                </td>
                                <td width="29%" bgcolor="#FFFFFF"></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">营业执照号码：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <label> 
                                        <input type="text" name="BusinessCard" id="BusinessCard" value="<?php echo $data_info['BusinessCard']; ?>" />
                                        <span class="red">*</span>
                                    </label>
                                </td>
                                <td width="29%" bgcolor="#FFFFFF"></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                <td colspan="2" bgcolor="#FFFFFF">
                                    <? if(!empty($data_info['BusinessCardImg'])) echo '<a href="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['BusinessCardImg']).'" target="_blank"><img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['BusinessCardImg']).'" border="0" height="300px" />';?>
                                </td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">身份证号码：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <?php echo $data_info['IDCard']; ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                <td colspan="2" bgcolor="#FFFFFF" >                               
                                    <? if(!empty($data_info['IDCardImg'])) echo '<a href="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['IDCardImg']).'" target="_blank"><img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['IDCardImg']).'" border="0" height="300px"  />';?>
                                </td>
                                <td></td>
                            </tr>
                             <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">经营许可证编号：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <?php echo $data_info['IDLicence']; ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"  align="right" valign="top">经营许可证：</td>
                                <td colspan="2" bgcolor="#FFFFFF">                               
                                    <? if(!empty($data_info['IDLicenceImg'])) echo '<a href="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['IDLicenceImg']).'" target="_blank"><img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['IDLicenceImg']).'" border="0" height="300px"  />';?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">GSP证书编号：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <?php echo $data_info['IDGP']; ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0" align="right" valign="top">GSP认证证书：</td>
                                <td colspan="2" bgcolor="#FFFFFF">                               
                                    <? if(!empty($data_info['GPImg'])) echo '<a href="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['GPImg']).'" target="_blank"><img src="'.RESOURCE_URL.str_replace("thumb_","img_",$data_info['GPImg']).'" border="0" height="300px"  />';?>
                                </td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </fieldset>
                <br style="clear:both;" />
                <fieldset class="fieldsetstyle">
                    <legend>审核说明</legend>
                    <div >
                        <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">审核人员：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <?php 
                                    	echo $_SESSION['uinfo']['username'].'['.$_SESSION['uinfo']['usertruename'].']';
                                    ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">审核备注：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <textarea style="height:90px;" id="remark" placeholder="请输入审核备注"></textarea>
                                </td>
                                <td></td>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
                            </tr>
                        </table>
                    </div>
                </fieldset>
                <br style="clear:both;" />
                <fieldset  class="fieldsetstyle">
                    <legend>基本资料</legend>
                    <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">

                        <tr>
                            <td width="15%" bgcolor="#F0F0F0"><div align="right">公司全称：</div></td>
                            <td width="35%" bgcolor="#FFFFFF">
                                <label>
                                    <?php echo $cinfo['CompanyName']; ?></label>
                            </td>
                            <td width="15%" bgcolor="#F0F0F0"><div align="right">简称：</div></td>
                            <td  bgcolor="#FFFFFF">
                                <?php echo $cinfo['CompanySigned']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">帐号前缀：</div></td>
                            <td bgcolor="#FFFFFF">
                                <?php echo $cinfo['CompanyPrefix']; ?>
                            </td>
                            <td bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyCity'];?></td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyContact'];?></td>
                            <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyMobile'];?>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyPhone'];?></td>
                            <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyFax'];?></td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyAddress'];?></td>
                            <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyEmail'];?></td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">客户网站：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyWeb'];?></td>
                            <td bgcolor="#F0F0F0"><div align="right">订货入口链接：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyUrl'];?></td>
                        </tr>
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                            <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyRemark'];?></td>
                            <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                    </table>
                </fieldset>

                <div class="rightdiv sublink" style="padding-right:20px;">
                    <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="通过" onclick="do_check_company('T');" />
                    <input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="不通过" onclick="do_check_company('F');" />
                    <input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
                </div>

            </div>
            <INPUT TYPE="hidden" name="referer" value ="" >
        </form>
        <br style="clear:both;" />
    </div>

    <?php include_once ("bottom.php");?>

    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <script type="text/javascript">
        $(function(){

            $("body").on('click','.blockOverlay',function(){
                $.unblockUI();
            });

        });

        function do_check_company(flag) {
            var sale = $('#sale').val();
            if(flag == 'T')
            {
            	if($('#data_CompanyArea').val()=="" || $('#data_CompanyArea').val()=="0")
            	{
            		$.blockUI({ message: "<p>请选择所属地区！</p>" });
            		return;
            	}
            	else if($('#data_CompanyIndustry').val()=="" || $('#data_CompanyIndustry').val()=="0")
            	{
            		$.blockUI({ message: "<p>请选择所属行业！</p>" });
            		return;
            	} 
            	else if($('#BusinessName').val() == '')
            	{
            		$.blockUI({ message: "<p>请输入公司名称！</p>" });
            		return;
            	}
            	else if($('#BusinessCard').val() == '')
            	{
            		$.blockUI({ message: "<p>请输入营业执照号码！</p>" });
            		return;
            	}
            }
            if(flag == 'F' && $('#remark').val() == ''){
            	$.blockUI({ message: "<p>请填写未通过原因</p>" });
        		return;
             }
            
            if(confirm('确认审核' + (flag == 'T' ? '通过' : '不通过'))) {
                $.post("do_manager.php",{
                    m:'company_check',
                    flag:flag,
                    id:"<?php echo $in['ID']; ?>",
                    area:$('#data_CompanyArea').val(),
                    industry:$('#data_CompanyIndustry').val(),
                    name:$('#BusinessName').val(),
                    card:$('#BusinessCard').val(),
                    sale:sale,
                    remark:$("#remark").val()
                },function(data) {
                    if(data == 'ok') {
                        $.blockUI({
                            message : '<p>操作成功!</p>'
                        });
                        setTimeout(function(){
                            window.location.href = "company_verify.php";
                        },710);
                    } else {
                        $.blockUI({
                            message : '<p>'+data+'</p>'
                        });
                    }
                },'text');
            }
        }
    </script>
    </body>
    </html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "┠-";
		$selectmsg = "";
		
		if($var['AreaParent']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParent'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer-2);
				if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}
?>