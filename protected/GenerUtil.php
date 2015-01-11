<?php
require_once 'config.php';

$CONNECTGUEST  = null;
$CONNECTUSER   = null;
function errorOccured() {
	header('HTTP/1.1 404 Not Found'); 
	header('Status: 404 Not Found');
	exit;
}

function getGuestConnect() {
	global $CONNECTGUEST;
	if ($CONNECTGUEST == null) {
		$CONNECTGUEST = new PDO("mysql:host=".HOST.";dbname=".DB,GUEST,GUESTPWD,array(PDO::ATTR_PERSISTENT => true));
	}
	return $CONNECTGUEST;
}
// sounds like connectionpool :) that array setting.
function getUserConnect() {
	global $CONNECTUSER;
	if ($CONNECTUSER == null) {
		$CONNECTUSER  = new PDO("mysql:host=".HOST.";dbname=".DB,BBSUSER,BBSUSERPWD,array(PDO::ATTR_PERSISTENT => true));
	}
	return $CONNECTUSER;
}

function getUsernameById($connect,$id) {
	$stmt = $connect->prepare("SELECT username FROM user WHERE user_id = :id");
	$stmt->bindParam(':id',$id);
	$stmt->execute();
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	return $res['username'];	
}

function getTotalIssue($connect,$sub_id) {
	$getTotalIssue = $connect->query("SELECT totalpart FROM subject where sub_id = $sub_id");
	$getTotalIssue->execute();
	$row = $getTotalIssue->fetch(PDO::FETCH_ASSOC);
	return intval($row['totalpart']);
}

function getTotalSubject($connect) {
	$getTotalSubject = $connect->query("SELECT * FROM totalsubject");
	$getTotalSubject->execute();
	$oldNum = $getTotalSubject->fetch(PDO::FETCH_ASSOC);
	return intval($oldNum['totalsub']);
}

function addTotalSubject($connect,$num) {
	$oldNum = getTotalSubject($connect);
	if ($oldNum + $num < 0) {
		return null;
	} else {
		$newNum = $oldNum + $num;
	}
	$setTotalSubject = $connect->prepare("UPDATE totalsubject SET totalsub = :newnum");
	$setTotalSubject->bindParam(':newnum',$newNum);
	$setTotalSubject->execute();
}

function getAutoIncrement($connect,$table) {
	$id = $connect->query("show table status like '$table'");
	$id->execute();
	$id = $id->fetch(PDO::FETCH_ASSOC);
	return $id['Auto_increment'];

}

//$connect:杩炴帴瀹炰緥 $sub_id:涓婚甯�$content:鍥炲鍐呭 $user_id:鍥炲浜�$whichpart:绗嚑妤�
function publishIssueHelper($connect,$sub_id,$content,$user_id,$whichpart) { // 姝ゅ嚱鏁板畬鎴愭渶绠�崟鐨勬彃鍏ユ搷浣溿�鍗曠嫭鍥炲笘鍐嶇敤浜嬪姟
	$issue_id = getAutoIncrement($connect,'issue'); // 鐢变簬鎻愪氦浜嬪姟鍚庡厛鎵цissue鐨勬彃鍏ワ紝鎵�互鎻愬墠鎻愬彇褰撳墠鎻掑叆鐨刬ssueid浠ヤ緵content浣跨敤
	
	$insertIssStmt = $connect->prepare("INSERT INTO issue(sub_id,content,user_id) VALUES (:sub_id,:content,:user_id)");
	$insertIssStmt->bindParam(':sub_id',$sub_id);
	$insertIssStmt->bindParam(':content',$content);
	$insertIssStmt->bindParam(':user_id',$user_id);
	$insertIssStmt->execute();
	
	$insertConStmt = $connect->prepare("INSERT INTO content(sub_id,issue_id,whichpart) VALUES (:sub_id,:issue_id,:whichpart)");
	$insertConStmt->bindParam(':sub_id',$sub_id);
	$insertConStmt->bindParam(':issue_id',$issue_id);
	$insertConStmt->bindParam(':whichpart',$whichpart);
	$insertConStmt->execute();
}

function getUserIdByName($connect,$name) {
	$stmt = $connect->prepare("SELECT user_id FROM user WHERE username = :name");
	$stmt->bindParam(':name',$name);
	$stmt->execute();
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	return $res['user_id'];
}

function publishSubject ($name,$content) {
	if (!isset($_SESSION['user'])) {
		header("Location:../index.php");
	}
	$start = '1';
	$connect = getUserConnect();
	if ($connect) {
		$connect->beginTransaction();
		$user_id = getUserIdByName($connect,$_SESSION['user']);
		$sub_id  = getAutoIncrement($connect, 'subject'); // for publish issue to use.
		$stmt    = $connect->prepare("INSERT INTO subject(name,user_id,totalpart) VALUES (:name,:user_id,:totalpart)");
		$stmt->bindParam(':name',$name);
		$stmt->bindParam(':user_id',$user_id);
		$stmt->bindParam(':totalpart',$start);
		
		$stmt->execute();
		addTotalSubject($connect,1); // 澧炲姞1娆″笘瀛愭�鏁般�
		publishIssueHelper($connect,$sub_id,$content,$user_id,'1');
		$connect->commit();
	}
}

function updateTotalPart($connect,$sub_id) {
	$stmt1 = $connect->prepare("SELECT totalpart FROM subject WHERE sub_id = :sub_id");
	$stmt1->bindParam(':sub_id',$sub_id);
	$stmt1->execute();
	$row = $stmt1->fetch(PDO::FETCH_ASSOC);
	$new = $row['totalpart'];
	$new = strval(intval($new) + 1);
	$stmt2 = $connect->prepare("UPDATE subject SET totalpart = :new WHERE sub_id = :sub_id");
	$stmt2->bindParam(':new',$new);
	$stmt2->bindParam(':sub_id',$sub_id);
	$stmt2->execute();
	return $new;
}

function publishIssue($sub_id,$username,$content) {
	$connect = getUserConnect();
	$connect->beginTransaction();
	$whichpart = updateTotalPart($connect,$sub_id);
	$user_id   = getUserIdByName($connect, $username);
	publishIssueHelper($connect, $sub_id, $content, $user_id, $whichpart);
	$connect->commit();
}


function getPageData($connect,$page) {
// 	$showStmt = $connect->prepare("SELECT * FROM subject ORDER BY sub_id DESC LIMIT :start,:offset");
	$start    = strval($page * intval(PAGEITEM));
	$offset   = strval(PAGEITEM);

// 	$showStmt->bindParam(':start',$start);
// 	$showStmt->bindParam(':offset',$offset);
	$showStmt = $connect->query("SELECT * FROM subject ORDER BY sub_id DESC LIMIT ".$start.",".$offset);
	$showStmt->execute();
	return $showStmt;
}

function getReplyNum($connect,$sub_id) {
	$stmt = $connect->query("SELECT totalpart FROM subject WHERE sub_id = $sub_id");
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	return $row['totalpart'];
}

function generateIssueItem($num,$nextPage = false,$prePage = false) {
	$url    = $_SERVER['PHP_SELF'];
	$sub_id = $_SERVER['QUERY_STRING'];
	$index  = strpos($sub_id,"&");
	if ($index) {
		$sub_id = substr($sub_id,0,$index);
	}
	$url   .= '?'.$sub_id;
	$str1   = '<a href="'.$url.'&'.'nowIssuePage='.$num.'">';
	if ($nextPage == true) {
		$str2 = '下一页';
	} else if ($prePage == true) {
		$str2 = '上一页';
	} else {
		$str2 = strval($num + 1);
	}
	return $str1.$str2.'</a>&nbsp;';	 
}

function generatePageItem($num,$nextPage = false,$prePage = false) {
	$str1 = '<a href="'.'./index.php?nowPage='.$num.'">';
	if ($nextPage == true) {
		$str2 = '下一页';
	} else if ($prePage == true) {
		$str2 = '上一页';
	} else {
		$str2 = strval($num + 1);
	}
	return $str1.$str2.'</a>&nbsp;';
}

function showPage($page,$totalPage,$function) {
	$res = '';
	if ($page > 0) {
		$res .= $function($page - 1,false,true); // 有上一页
	}
	$start = $page - 5 >= 0 ? $page - 5 : 0;
	$fin   = $page + 5 >= $totalPage ? $totalPage - 1 : $page + 5;
	for ($i = $start;$i < $page;$i++) {
		$res .= $function($i);
	}
	$res .= ($page + 1).'&nbsp;';
	for ($i = $page + 1;$i <= $fin;$i++) {
		$res .= $function($i);
	}
	if ($page < $totalPage - 1) {
		$res .= $function($page + 1,true,false); // 有下一页
	}
	echo $res;
}

// 閮戒娇鐢╣uest鏉ヨ繛鎺ュ苟鏄剧ず甯栧瓙銆傜鐞嗗憳鍙互杩涘叆绠＄悊妯″紡锛屼竴鑸敤鎴峰湪鏈�悗鍒ゆ柇鏄惁鏄剧ず鍙戝笘閫夐」锛屽疄闄呴〉鏁颁粠0寮�锛屾樉绀鸿鍔� 
function showSubject($page) {
	$connect = getGuestConnect();
	if ($connect) {
		$totalItem = getTotalSubject($connect);
		$totalPage = intval($totalItem / intval(PAGEITEM) + ($totalItem % intval(PAGEITEM) == 0 ? 0 : 1));
		if ($page >= $totalPage) {
			errorOccured();
		}
		$pageData  = getPageData($connect,$page);
		$pageData->execute();
		$count = 1;
		while ($row = $pageData->fetch(PDO::FETCH_ASSOC)) {
			echo $count.' <a href=issue.php?'.'sub_id='
				 .$row['sub_id']."> ".$row['name'].'</a>'." "
				 .getUsernameById($connect, $row['user_id'])." "
				 .getReplyNum($connect,$row['sub_id'])." "
				 .$row['pubdate'].'<br />';
			$count ++;
		}
		showPage($page,$totalPage,'generatePageItem');	
	}
}

function showIssueContent($content,$pubdate,$username,$whichpart) {
	echo "$username ".$pubdate ." $whichpart "." 楼<br />".
		 $content ."<br />"."<hr>";	
}

function getContentData($connect,$sub_id,$page) {
	$start     = strval($page * intval(PAGEITEM));
	$offset    = strval(PAGEITEM);
	$totalPage = getTotalIssue($connect, $sub_id); 
	$stmt1 = $connect->prepare("SELECT issue_id,whichpart FROM content WHERE sub_id = :sub_id ORDER BY whichpart LIMIT ".$start.",$offset");
	$stmt1->bindParam(':sub_id',$sub_id);
	$stmt1->execute();
	$stmt2 = $connect->prepare("SELECT content,pubdate,user_id FROM issue WHERE issue_id = :issue_id");
	$stmt3 = $connect->prepare("SELECT username FROM user WHERE user_id = :user_id");
	while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
		$whichpart = $row['whichpart'];
		$stmt2->bindParam(':issue_id',$row['issue_id']);
		$stmt2->execute();
		$row = $stmt2->fetch(PDO::FETCH_ASSOC);
		$stmt3->bindParam(':user_id',$row['user_id']);
		$stmt3->execute();
		$username = $stmt3->fetch(PDO::FETCH_ASSOC);
		$username = $username['username'];
		showIssueContent($row['content'],$row['pubdate'],$username,$whichpart);
	}
	showPage($page,$totalPage,'generateIssueItem');	
}

function showIssue($sub_id,$page) {
	$connect = getGuestConnect();
	if ($connect) {
		$contentData = getContentData($connect,$sub_id,$page);
	}	
}

function showSubTitle($sub_id) {
	$connect = getGuestConnect();
	$stmt = $connect->prepare("SELECT name FROM subject WHERE sub_id = :sub_id");
	$stmt->bindParam(':sub_id',$sub_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	return $row['name'];
}

?>