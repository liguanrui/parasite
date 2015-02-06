<?php
header("Content-Type:text/html;charset=utf-8");
error_reporting(0);
set_time_limit(9999);
class parasite{

    protected $folderpath;
    protected $keywords;
    protected $contents;
    protected $sites;
    protected $pagePrefix;
    protected $contextPrefix;

    function __construct(){
        $this->folderpath = "category/";
        $this->website= "http://127.0.0.1/parasite/service/";
        $this->keywords = file($this->website."key.txt");
        $this->contents = file($this->website."content.txt");
        $this->sites = file($this->website."sites.txt");
        $this->pagePrefix ="page-";
        $thia->contextPrefix ="context-";
    }


    function index(){
    	//$this->init();
    	$this->getTemplate();
    }


    function getTemplate(){
    	//这里可以加一个随机获取模板
    	$templateLink = $this->website."template/tamplate_01/";
    	$template = file_get_contents($templateLink."index.html");
    	
    	//getCss
    	$this->getCss($template, $templateLink);
    	//getJs
    	$this->getJs($template, $templateLink);
    	//getImage
    	$this->getImage($template, $templateLink);
    	
    	
    	//getTitle
    	
    	//从关键字中返回一个随机数组：
    	$menuTotal = rand(10, 20);//生成10~20个菜单
    	$menuArr = array_rand($this->keywords,$menuTotal);
    	 
    	$perPageNum = 30;
    	$pageTotal = ceil( count($this->keywords)/$perPageNum );

    	$titleKey = $menuArr[ array_rand( $menuArr,1)];
    	 
    	$titleStr = $this->keywords[$titleKey]." 主页";
    	
    	$this->WiterLog("执行自动化创建网站".$titleStr."开始");
    	
    	 
    	
    	//生成主页
    	for($i=1;$i<=$pageTotal;$i++){
    		//生成分页
    		$pageStr = $this->getPageStr($i, $pageTotal);
    		
    		//生成菜单
    		//shuffle($menuArr);
    		$menuStr = "";
    		$linkStr = "";
    		foreach($menuArr as $k=>$menuKey){
    			 
    			$contextKey=$k+1;
    			if($titleKey == $menuKey){
    				$menuStr .= "<a href='{$this->contextPrefix}{$contextKey}.html' class='list-group-item active'>".$this->keywords[$menuKey]."</a>";
    			}else{
    				$menuStr .= "<a href='{$this->contextPrefix}{$contextKey}.html' class='list-group-item'>".$this->keywords[$menuKey]."</a>";
    			}
    			
    			$linkStr .= "<a class='col-md-2' href='{$this->contextPrefix}{$contextKey}.html'>".$this->keywords[$menuKey]."</a>";
    			
    		}
    		
    		//生成面包屑
    		$breadCrumbStr = "<li><a href='#'>".$titleStr."</a></li>".
	  						 "<li><a href='#'>文章</a></li>".
	  						 "<li class='active'>page".$i."</li>";
    		
    		//生成内容
    		$contantStr = "<table class='table'>";
    		for($n=0;$n<$perPageNum;$n++){
    			$key = $n + $perPageNum*($i-1);
    			$contantStr .="<tr><td>".$this->keywords[$key]."</td></tr>";			
    		}
    		$contantStr .= "</table>";
    		
    		$template = str_replace('<{$title}>', $titleStr, $template);
    		$template = str_replace('<{$page}>', $pageStr, $template);
    		$template = str_replace('<{$sideMenu}>', $menuStr, $template);
    		$template = str_replace('<{$contant}>', $contantStr, $template);
    		$template = str_replace('<{$breadCrumb}>', $breadCrumbStr, $template);
    		$template = str_replace('<{$link}>', $linkStr, $template);
    		$this->WiterFile($this->pagePrefix.$i.".html",$template);
    		$this->WiterLog("执行自动化创建分页".$i."开始");
    		die();
    	}

   
    }
    
    /**
     * 获取分页样式和html
     * @param unknown $i
     * @param unknown $pageTotal
     * @return string
     */
    function getPageStr($i,$pageTotal){
    	
    	$pageShow = 7;//显示页码的数目
    	$per = ($i-1) ? ($i-1) : 1;
    	$sub = ($i+1)>$pageTotal ? $pageTotal : ($i+1);
    	$startNum = $i-(floor($pageShow/2));
    	$start = $startNum > 0 ? $startNum : 1;
    	$endNum = $i+(floor($pageShow/2));
    	$start = $endNum < $pageTotal ? $start : $pageTotal-$pageShow+1;
    	
    	$pageStr = "";
    	$pageStr .="<li><a href='{$this->folderpath}{$this->pagePrefix}1.html'>&laquo;</a></li>".
    			"<li><a href='{$this->folderpath}{$this->pagePrefix}{$per}.html'>&lsaquo;</a></li>";
    	
    	for($n=0;$n<$pageShow;$n++){
    		$num=$start+$n;
    		if($num==$i){
    			$pageStr .="<li class='active'><a href='{$this->folderpath}{$this->pagePrefix}{$num}.html'>{$num}</a></li>";
    		}else{
    			$pageStr .="<li><a href='{$this->folderpath}{$this->pagePrefix}{$num}.html'>{$num}</a></li>";
    		}
    	}
    	
    	$pageStr .="<li><a href='{$this->folderpath}{$this->pagePrefix}{$sub}.html'>&rsaquo;</a></li>".
    			"<li><a href='{$this->folderpath}{$this->pagePrefix}{$pageTotal}.html'>&raquo;</a></li>";
    	
    	return $pageStr;
    }
    
    /**
     * 正则匹配template里面的内容再获取css
     * @param unknown $template
     * @param unknown $templateLink
     * @form：<link href="css/main.css"  rel="stylesheet">
     */
	function getCss($template,$templateLink){
	    $regExp_css = '/(?:(<link.+href=\")((?!http).+\.css){1}(\".*(?:type=\"text\/css\"){0,1}.*>))|(?:(<link.+(?:type=\"text\/css\"){1}.*href=\")((?!http).+\.css){1}(\".*>))/i';
	    preg_match_all($regExp_css ,$template, $cssFile);
	    foreach($cssFile[2] as $k=>$fileDir){
	    	$fileNameArr = explode("/", $fileDir);
	    	$fileName = $fileNameArr[ (count($fileNameArr)-1) ];
	    	$fileString = file_get_contents($templateLink."css/".$fileName);
	    	$this->WiterFile("css/".$fileName,$fileString);
	    	$this->WiterLog("生成css文件".$fileName."成功！");
	    }
	}
     
  
    /**
     * 正则匹配template里面的内容再获取js
     * @param unknown $template
     * @param unknown $templateLink
     * @from <script src="js/bootstrap.min.js"></script>
     */
    function getJs($template,$templateLink){
	    $regExp_js = '/(?:(<script src=\")((?!http).+\.js){1}(\".*(?:type=\"text\/js\"){0,1}.*>))|(?:(<script.+(?:src=\"text\/js\"){1}.*src=\")((?!http).+\.js){1}(\".*>))/i';
	    preg_match_all($regExp_js ,$template, $jsFile);
	    foreach($jsFile[2] as $k=>$fileDir){
	    	$fileNameArr = explode("/", $fileDir);
	    	$fileName = $fileNameArr[ (count($fileNameArr)-1) ];
	    	$fileString = file_get_contents($templateLink."js/".$fileName);
	    	$this->WiterFile("js/".$fileName,$fileString);
	    	$this->WiterLog("生成js文件".$fileName."成功！");
	    }
    }
     
     
    
    /**
     * 正则匹配template里面的内容再获取image
     * @param unknown $template
     * @param unknown $templateLink
     * @from <img src="image/bg.png">
     */
    function getImage($template,$templateLink){
	    $regExp_img = '/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i';
	    preg_match_all($regExp_img ,$template, $imgFile);
	    foreach($imgFile[1] as $k=>$fileDir){
	    	$fileNameArr = explode("/", $fileDir);
	    	$fileName = $fileNameArr[ (count($fileNameArr)-1) ];
	    	$fileString = file_get_contents($templateLink."image/".$fileName);
	    	$this->WiterFile("image/".$fileName,$fileString);
	    	$this->WiterLog("生成image文件".$fileName."成功！");
	    }
    }
    
    /**
    * @label 初始化
    **/
    function init(){
    	if (file_exists($this->folderpath)) {	
	        if( $this->deldir($this->folderpath) ){
	        	$this->WiterLog("执行删除".$this->folderpath."目录,操作成功");
	        }else{
	        	$this->Error("执行删除".$this->folderpath."目录,操作失败");
	        }
    	}
        if(mkdir($this->folderpath)){
        	$this->WiterLog("执行创建".$this->folderpath."目录,操作成功");
        }else{
        	$this->Error("执行创建".$this->folderpath."目录,操作失败");
        }
    }
    
    /**
     * 写文件
     * @param unknown $fDir
     * @param unknown $fString
     */
    protected function WiterFile($fDir,$fString){
    	$fNameArr = explode("/", $fDir);
    	if(count($fNameArr)==2){
    		mkdir($this->folderpath."/".$fNameArr[0]);
    		$fp = fopen($this->folderpath."/".$fNameArr[0]."/".$fNameArr[1], "w");
    	}elseif(count($fNameArr)==1){
    		$fp = fopen($this->folderpath."/".$fNameArr[0], "w");
    	}
    	fputs($fp, $fString);
    	fclose($fp);
    }
    
    /**
     * 模拟post进行url请求
     *
     * @param string $url
     * @param string $param
     */
    protected function requestPost($url = '', $param = '') {
    	if (empty($url) || empty($param)) {
    		return false;
    	}
    
    	$postUrl = $url;
    	$curlPost = $param;
    	$ch = curl_init();//初始化curl
    	curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    	curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    	curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    	$data = curl_exec($ch);//运行curl
    	curl_close($ch);
    
    	return $data;
    }
    
    /**
     * @label 写日志操作
     * @param unknown $msg
     */
    protected function WiterLog($msg){
    	$time = date("Y-m-d H:i");
    	$msg = $time."\r\n".$msg."\r\n\r\n";
    	echo $msg;
    	//$this->requestPost执行一下远程记录
    }
    
    /**
     * 抛出错误
     * @param unknown $msg
     */
    protected function Error($msg){
    	$time = date("Y-m-d H:i");
    	$msg = $time."\r\n".$msg."\r\n\r\n";
    	echo $msg;
    	die();//终止
    }
    
    /**
     * 警告
     * @param unknown $msg
     */
    protected function Warning($msg){
    	$time = date("Y-m-d H:i");
    	$msg = $time."\r\n".$msg."\r\n\r\n";
    	echo $msg;
    }
    

    /**
     * @label 删除目录下所有的文件：递归一阶
     * @param unknown $dir
     * @return boolean
     */
    protected function deldir($dir) {
    	$dh=opendir($dir);
    	while ($file=readdir($dh)) {
    		if($file!="." && $file!="..") {
    			$fullpath=$dir."/".$file;
    			if(!is_dir($fullpath)) {
    				unlink($fullpath);
    			} else {
    				$this->deldir($fullpath);
    			}
    		}
    	}
    	 
    	closedir($dh);
    	//删除当前文件夹：
    	if(rmdir($dir)) {
    		return true;
    	} else {
    		return false;
    	}
    }

    
} 

/**
 * 模拟路由
 * @var unknown_type
 */
$mObject = new parasite();
function getAction() {
    if (isset ( $_GET ['action'] )) {
        $action = trim ( $_GET ['action'] );
        unset($_GET ['action'] );
        unset($_REQUEST['action']);//测试用
    } else {
        $action ="index";// 有个默认的Action行为
    }
    return $action;
}

$action=getAction();
call_user_func ( array(
$mObject,
$action
) );

?>
