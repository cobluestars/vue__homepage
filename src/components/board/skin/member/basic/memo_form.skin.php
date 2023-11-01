<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.memo.css">', 0);

$recv_mb = get_member($me_recv_mb_id);
$p_icon = get_member_profile_img($me_recv_mb_id);

//쪽지알림 업데이트
$sql3 = " update `{$g5['member_table']}` set mb_memo_cnt = '".get_memo_not_read($member['mb_id'])."' where mb_id = '{$member['mb_id']}' ";
sql_query($sql3);

//대화창을 열면 기존 읽지 않은 쪽지를 업데이트 한다.
$aaa = "select * from {$g5['memo_table']} where me_recv_mb_id ='".$member['mb_id']."' and me_send_mb_id = '".$recv_mb['mb_id']."' and me_read_datetime = '0000-00-00 00:00:00'";
$bbb = sql_query($aaa);

//한개씩 읽은 날짜를 업데이트 해준다.
for($i=0;$ccc=sql_fetch_array($bbb);$i++) {
    $up = "update {$g5['memo_table']} set me_read_datetime = '".G5_TIME_YMDHIS."' where me_id = '".$ccc['me_id']."'";
    sql_query($up);
}

//가장 큰 결과 값을 구함
$que = "select max(me_id) as max from {$g5['memo_table']} where (me_recv_mb_id ='".$recv_mb['mb_id']."' and me_send_mb_id = '".$member['mb_id']."') or (me_recv_mb_id = '".$member['mb_id']."' and me_send_mb_id = '".$recv_mb['mb_id']."')";
$max = sql_fetch($que); 

//전체 카운터 
$que = "select count(me_id) as cnt from {$g5['memo_table']} where (me_recv_mb_id ='".$recv_mb['mb_id']."' and me_send_mb_id = '".$member['mb_id']."') or (me_recv_mb_id = '".$member['mb_id']."' and me_send_mb_id = '".$recv_mb['mb_id']."')";
$cnt = sql_fetch($que);
$total = $cnt['cnt']; //총갯수
$limit = 10; // 출력할 갯수
//$limit = $cnt['cnt']; // 출력할 갯수

$from_record = $total - $limit;
if($from_record < 0) {
    $limit_record = $limit_record + $from_record;
    $from_record = 0;
}

$sql = "select * from {$g5['memo_table']} where (me_send_mb_id ='".$recv_mb['mb_id']."' and me_recv_mb_id = '".$member['mb_id']."') or (me_send_mb_id = '".$member['mb_id']."' and me_recv_mb_id = '".$recv_mb['mb_id']."') order by me_send_datetime asc limit  {$from_record}, {$limit}";
$res = sql_query($sql);

// 날짜 계산 함수
/*
function passing_time($me_send_datetime) {
	$time_lag = time() - strtotime($me_send_datetime);
	
	if($time_lag < 60) {
		$posting_time = "방금";
	} else if($time_lag >= 60 and $time_lag < 3600) {
		$posting_time = floor($time_lag/60)."분 전";
	} else if($time_lag >= 3600 and $time_lag < 86400) {
		$posting_time = floor($time_lag/3600)."시간 전";
	} else if($time_lag >= 86400 and $time_lag < 2419200) {
		$posting_time = floor($time_lag/86400)."일 전";
	} else {
		$posting_time = date("y-m-d", strtotime($me_send_datetime));
	} 
	
	return $posting_time;
}
*/
?>


<div id="chat-header">
    <div>
        <div class="mo_tits">
            <span class="badge3"><?php echo $recv_mb['mb_nick'];?>님과의 대화</span>
        </div>
        <div class="mo_bdgs">
            <a href="javascript:void(0);" id="btn_close2" onclick="javascript:self.close();">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -5 10 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus w-5 h-5 sm:w-6 sm:h-6"><line x1="13" y1="12" x2="3" y2="2"></line><line x1="13" y1="2" x2="3" y2="12"></line></svg>
            </a>
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<div id="chat-body">
    
    <?php
    if($total > $limit) { //총갯수가 출력할 갯수보다 많으면 이전 버튼을 만들어 준다
        echo '<div class="chat_history_div_wrap" id="chat_history_div_wrap">';
        echo '<div class="chat_history_div">';
        echo '<button type="button" id="chat-history" style="">이전대화 10개 보기</button>';
        echo '</div>';
        echo '</div>';
    }
    ?>
    
    <ul id="chat-list">
        
    <?php if($total <= 0) { ?>
    <div class="chktotal"><?php echo $recv_mb['mb_nick'];?>님과 대화를 시작해보세요!</div>
    <?php } ?>
        
        <?php
        for($i=0;$row=sql_fetch_array($res);$i++) {
            

            $newDate = substr($row['me_send_datetime'],0,10);
            
            if($row['me_send_mb_id'] == $member['mb_id']) {
                $cls = "recv";
                $cls_box = "bubble_recv";
                
            } else {
                $cls = "send";
                $cls_box = "bubble_send";
            }
            
            if($newDate != $chkDate) {
                echo '<li id="'.$newDate.'" class="chkdate">' . $newDate.'</li>';
            }
            echo '<li id="chat_list_'.$row['me_id'].'" class="'.$cls.'">';
            if($row['me_send_mb_id'] != $member['mb_id']) {
                
                if($recv_mb['mb_level'] == "10") { // 관리자의 경우는 프로필 볼 수 없도록 처리
                    echo '<span class="p_icon">'.$p_icon.'</span>';
                } else {
                    echo '<a href="'.G5_BBS_URL.'/profile.php?mb_id='.$recv_mb['mb_id'].'" target="_blank" class="p_icon win_profile">'.$p_icon.'</a>';
                }
                
                echo '<span class="p_nick"> '.$recv_mb['mb_nick'].'</span>';
            }

            echo '<div class="'.$cls_box.'">';
            echo $row['me_memo'];
            echo '</div>'; 
            
            echo '<div class="chat_time_'.$cls.'">';
            
            /* 수신확인 부분, 실시간 구현 필요 */
            if($row['me_send_mb_id'] == $member['mb_id']) {
                if(substr($row['me_read_datetime'],0,1) == 0) {
                    echo "<span id='am".$row["me_id"]."' style='color:#FF0000; font-weight:bold;'>1</span>";
                } else {
                    echo "";
                }
            }
            /* 수신확인 실시간처리 구현필요 */ 

            echo substr($row['me_send_datetime'],10,6);
            echo '</div>';
            
            if($row['me_send_mb_id'] == $member['mb_id']) {
                
            echo '<div style="position: absolute; margin-top:-62px; margin-left:-20px;"><a href="'.G5_BBS_URL.'/ajax.memo_del.php?me_id='.$row['me_id'].'&del_id='.$recv_mb['mb_id'].'" class="chatroom-cnt2 badge3"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash w-4 h-4 mr-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a><div>';
                
            }
                       
            
            echo '</li>';
            

            
            $chkDate = $newDate;
            $pos_id = $row['me_id']; 
        }
        $toDate = date("Y-m-d");
        if($chkDate != $toDate) {
            echo '<li id="'.$toDate.'" class="chkdate">' . $toDate.'</li>';
        }
        ?>
    </ul>
</div>

<div id="chat-footer">

    <div class="chat_message_div">
        <textarea id="chat-message" name="cmt_text" placeholder="메세지를 입력하세요."></textarea>
    </div>
    <div class="chat_message_btn_div">

        <div id="btn-ref" title="새로고침">
            <i class="fa fa-refresh" aria-hidden="true"></i>
        </div>

        <div id="btn-chat" title="보내기">
            <i class="fa fa-paper-plane" aria-hidden="true"></i>
        </div>
    </div>
    <div style="clear:both"></div>
    
</div>

<script>
var pos_id = "<?php echo $pos_id;?>";
var chkDate = "<?php echo $chkDate;?>";
var send_mb_id = "<?php echo $member['mb_id'];?>";
var recv_mb_id = "<?php echo $recv_mb['mb_id'];?>";
var max_id = "<?php echo $max['max'];?>";
//var limit_record = "<?php echo $total - $limit;;?>";
var limit_record = "<?php echo $limit;?>";
var from_record = "<?php echo $from_record;?>";
var p_icon = '<?php echo $p_icon;?>';
var p_nick = "<?php echo $recv_mb['mb_nick'];?>";

function chat_refresh() { 
	
    $.ajax({

        type: "POST",
        data: {act:'refresh',send_mb_id:send_mb_id,recv_mb_id:recv_mb_id,max_id:max_id},
        url: '<?php echo G5_BBS_URL; ?>/ajax.memo.php',
        success: function(data) {

            var html = ''; 

            $.each(data.data, function(i, $i) { 
                var html = '';
                if ($i.me_send_mb_id == send_mb_id) { 
                    var cls = "recv";
                    var cls_box = "bubble_recv"; 
                } else {
                    var cls = "send"; 
                    var cls_box = "bubble_send"; 
                }
                html +='<li id="chat_list_'+$i.me_id+'" class="'+cls+'">'; 
                if($i.me_send_mb_id != send_mb_id) { 
                    html += '<span class="p_icon">'+p_icon+'</span>'; 
                    html += '<span class="p_nick">'+p_nick+'</span>'; 
                }
                
                html += '<div class="'+cls_box+'">';
                html += $i.me_memo;
                html += '</div>';
                html += '<div class="chat_time2_'+cls+'">';
                
               
                if ($i.me_send_mb_id == send_mb_id) {
	                if($i.me_read_datetime.substr(0,1) == 0) {
					//if($i.me_send_datetime.substr(0,1) == 0) {
                        html += "<span id='bm"+$i.me_id+"' style='color:#FF0000; font-weight:bold;'>1</span>";
                    } else {
                        //html += '';
                    }
                }        
                
                
                html += $i.me_send_datetime.substr(10,6);
                html += '</div>';
                    
                if($i.me_send_mb_id == send_mb_id) {
                
                html += '<div class="del_div"><a href="<?php echo G5_BBS_URL ?>/ajax.memo_del.php?me_id='+$i.me_id+'&del_id=<?php echo $recv_mb['mb_id'] ?>" class="chatroom-cnt2 badge3"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash w-4 h-4 mr-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a><div>';

                }


                html += '</li>';

                $('#chat-list').append(html);
				//보낸사람 숫자1 없애기

				check_Read();
				
				//ajax read체크 
				//$("#am"+$i.me_id).html(""); 

                max_id = data.max.max;

                if ($i.me_id) {
                     move_page();
                }
            });
           
        }
    });
    return false;

}

$("#chat-history").click(function() {
    $.ajax({
        type: "POST",
        data: {act:'history',send_mb_id:send_mb_id,recv_mb_id:recv_mb_id,from_record:from_record,limit_record:limit_record},
        url: '<?php echo G5_BBS_URL; ?>/ajax.memo.php',
        success: function(data) {
            var html = '';
            $.each(data.data, function(i, $i) {
                pos_id = $i.me_id;
                newDate = $i.me_send_datetime.substr(0,10);
                if ($i.me_send_mb_id == send_mb_id) {
                    var cls = "recv";
                    var cls_box = "bubble_recv";
                } else {
                    var cls = "send";
                    var cls_box = "bubble_send";
                }
                if  (newDate != chkDate) {
                    if($('#'+newDate).length > 0) {
                        $('#'+newDate).remove();
                    }
                    html +='<li id="'+newDate+'" class="chkdate">'+newDate+'</li>';
                } else {
                    if($('#'+newDate).length > 0) {
                       $('#'+newDate).remove();
                       html +='<li id="'+newDate+'" class="chkdate">'+newDate+'</li>';
                    }
                }
                html +='<li id="chat_list_'+$i.me_id+'" class="'+cls+'">';
                if($i.me_send_mb_id != send_mb_id) {
                    html += '<span class="p_icon">'+p_icon+'</span>';
                    html += '<span class="p_nick">'+p_nick+'</span>';
                }
                html += '<div class="'+cls_box+'">';
                html += $i.me_memo;
                html += '</div>';
                html += '<div class="chat_time2_'+cls+'">'+$i.me_send_datetime.substr(10,6)+'</div>';
                
                if($i.me_send_mb_id == send_mb_id) {
                
                html += '<div class="del_div"><a href="<?php echo G5_BBS_URL; ?>/ajax.memo_del.php?me_id='+$i.me_id+'&del_id=<?php echo $recv_mb['mb_id'] ?>" class="chatroom-cnt2 badge3"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash w-4 h-4 mr-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a><div>';

                }
                
                html += '</li>';
                chkDate = newDate;
            });
            $('#chat-list').prepend(html);

			

            from_record = data.from_record; 
            if (from_record == 0) {
                //$('#chat-history').hide();
                $('#chat_history_div_wrap').hide();
            }
            movePos();
        }
    });
    return false;
});

    
$("#btn-chat").click(function() {
    $("#chat-message").focus();
    var message = $("#chat-message").val();
    if (message == "")
    {
        alert('메세지를 입력하세요');
        return false;
    }
    $("#chat-message").val('');
    send_message(message);

});


$("#btn-ref").click(function() {   
    window.location.reload();
});


function send_message(msg) {
    $.ajax({
        type: "POST",
        data: {act:'update',send_mb_id:send_mb_id,recv_mb_id:recv_mb_id,me_memo:msg,recv_mb_level:<?php echo $recv_mb['mb_level'];?>},
        url: '<?php echo G5_BBS_URL; ?>/ajax.memo.php',
        success: function(data) {
            if(data['error_msg'].length > 0) {
                var html = '';
                html +='<li class="">이용하실 수 없습니다.('+data['error_msg']+')</li>';
                $('#chat-list').append(html);
                return false;
            }
            chat_refresh(); 
        }
    });
}

$(function() {
    $('#chat-message').on('keydown', function(event) {
        if (event.keyCode == 13)
            if (!event.shiftKey){
                event.preventDefault();
                $('#btn-chat').trigger('click');
            }
    });
});

/*
$('#chat-body').scroll(function() {
    if($('#chat-body').scrollTop() == 0) {
        $('#chat-history').trigger('click');
    }
});

function movePos(){
    var pos = pos_id;
    document.getElementById('chat_list_'+pos).scrollIntoView();
    
}
*/

function move_page() {
    $('#chat-body').animate({scrollTop:$('#chat-body')[0].scrollHeight}, 0);
}

window.onload = function () { 
    move_page();
};

function check_Read() 
{
	 //전체데이터 가져와서 처리
	  $.post("<?php echo G5_BBS_URL; ?>/ajax.memo_read.php", {sender: "<?php echo $recv_mb['mb_id'];?>",receiver: "<?php echo $member['mb_id'];?>",from_record:"<?php echo $from_record;?>"}, function(result){ 
			var dd=result.split("--");
			for (i = 0; i < dd.length; i++) {
				console.log( dd[i] ); 
				var rd=dd[i].split("|");
				//읽음
				if( rd[1]=="Y"){
					$("#am"+rd[0]).html("");
					$("#bm"+rd[0]).html("");					
				}
			}

			//location.reload();

	  });

}

setInterval( function() {
    chat_refresh();	
	check_Read(); 
	

}, 10000 ); //10초에 갱신

</script>
