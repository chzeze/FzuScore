<?php
        	
namespace Addons\Fzuscore\Model;
use Home\Model\WeixinModel;
        	
/**
 * Fzuscore的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Fzuscore' ); // 获取后台插件的配置参数
		$isbind=$this->_isBind();
		if($isbind){//已经绑定		
				if($dataArr['Content']=='6'){
						$keywordArr['step']='input';
						set_user_status("Fzuscore",$keywordArr);//缓存自定义关键字
						$this->replyText($config['desc']);
					}
				if($keywordArr['step']=='input'){
					if($dataArr['Content']=='退出'){
							$this->replyText($config['menu']);
							return false;
						}
						else{
								$keywordArr['step']='input';
								set_user_status("Fzuscore",$keywordArr);//缓存自定义关键字
								$score=$this->_curlScore($isbind['username'],$isbind['pwd']);//根据账号密码登录教务处查询
  								$this->replyText($score."=========\n成绩查询结束！\n退出查询请点击菜单中【退出功能】或回复【退出】");
						}
					}
				
		}
		else{
			$this->replyText('未绑定教务处账号，绑定请回复：bind#您的学号#您的密码');
			return ;
		}
	}
	
	//验证是否已经绑定账号
	function _isBind(){
		$map['token']=get_token();
		$map['openid']=get_openid();
		$res=M('bind')->where($map)->find();
		if($res)	//已经绑定
			return $res;//返回查询结果	
		else	//未绑定教务处账号
			return $res;
	}
	
	//curl获取成绩信息
	function _curlScore($user,$pwd){
		
		$url = "http://59.77.226.32/logincheck.asp"; 
		$User_Agent="Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";	
		$post_data = "muser=".$user."&passwd=".$pwd;	//设置查询参数
		$refer="http://jwch.fzu.edu.cn/";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);		//设置URL
		curl_setopt($ch, CURLOPT_HEADER, true);		//设置显示响应头
		curl_setopt($ch,CURLOPT_USERAGENT,$User_Agent); //设置代理浏览器
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //把CRUL获取的内容赋值到变量,不直接输出
		curl_setopt($ch, CURLOPT_TIMEOUT,5);		//微信5秒超时处理
		curl_setopt($ch,CURLOPT_REFERER,$refer);	//设置来源网站
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//设置重定向
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);//设置参数
		$content = curl_exec($ch);
		curl_close($ch);
	
		$headArr = explode("\r\n", $content);		//解析url响应头
		foreach ($headArr as $loop) {
			//echo htmlspecialchars($loop)."<br />";
		  if(strpos($loop, "http://59.77.226.35/default.aspx?id=") !== false){//获取id值
				$id = trim(substr($loop, 46));
		  }
		  if(strpos($loop, "Set-Cookie") !== false){//找到Cookie
				$cookie = trim(substr($loop, 12));
		  }
		}
		$rurl="http://59.77.226.35/student/xyzk/cjyl/score_sheet.aspx?id=".$id;//最后需要采集数据的页面
		
		/*echo "rUrl:<br>".$rurl;
		echo "<br />";
		echo "cookie:<br>".$cookie."<br />";*/
		
		
		//结果处理
		$url = $rurl; 
		$User_Agent="Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";	
		$refer="http://jwch.fzu.edu.cn/";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);			//设置URL
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);		//设置上面取得的cookies值
		curl_setopt($ch,CURLOPT_USERAGENT,$User_Agent); //设置代理浏览器
		curl_setopt($ch, CURLOPT_TIMEOUT,5);			//微信5秒超时处理
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //把CRUL获取的内容赋值到变量,不直接输出
		curl_setopt($ch,CURLOPT_REFERER,$refer);		//设置来源网站
		$content = curl_exec($ch);
		curl_close($ch); 
		
		//解析html
		//echo "<br />解析：<br />";
		$content=strip_tags($content);//去除html标签	
		$content=trim(substr(trim($content),1710));	
		//echo $content;
		
		$score='';
		$arr=explode("本专业",$content);
		foreach($arr as $key=>$rs){
			$res=explode("                   ",$rs);
			/*foreach($res as $key=>$ans){
				if($ans!='')
				 echo "[".$key."]=".$ans." ";	
			}*/
			if($res!=''&&$rs!=''&&$key<25)
			  $score.=$res[2]." ".$res[4];
		}
		return $score;	
			
	}
}
        	