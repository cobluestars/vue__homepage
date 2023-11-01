<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.memo.css">', 0);

//회원정보 불러오기
$sql = " select * from $g5[member_table] group by mb_id order by mb_id asc"; 
$result = sql_query($sql); 
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $member_skin_url ?>/memo_search.js"></script>

<div id="memo">

    <div id="memo-body">

        <div id="memo-right">
            <!-- 검색 -->
            <div id="memo-search">
                <form onsubmit="return false;">
                    
                    <input type="text" name="mb_nick" id="sch_stx" class="sec_inp" placeholder="대화 추가하기(닉네임입력)">
                    
                    <button type="submit" id="search-submit" onclick="chat_invite();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus w-5 h-5 sm:w-6 sm:h-6"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>

                    </button>

                    
                    <a href="javascript:void(0);" id="btn_close2" onclick="javascript:self.close();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -5 10 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus w-5 h-5 sm:w-6 sm:h-6"><line x1="13" y1="12" x2="3" y2="2"></line><line x1="13" y1="2" x2="3" y2="12"></line></svg>
                    </a>
                </form>
            </div>
            <div id="memo-chatlist">
                <ul class="memo-chatlist">
                <?php
                $sql = "select *, 
                        if(me_recv_mb_id='".$member['mb_id']."',me_send_mb_id,me_recv_mb_id) as me_chat_id 
                        from (select * from {$g5['memo_table']} order by me_id desc limit 0,99999) as tempmemo WHERE (me_recv_mb_id ='".$member['mb_id']."' or me_send_mb_id ='".$member['mb_id']."') 
                        group by me_chat_id
                        order by me_id desc limit 0,99999";
                $res = sql_query($sql);
                    
                
                    
                for($i=0;$row=sql_fetch_array($res);$i++) {
                    $readed = (substr($row['me_read_datetime'],0,1) == 0) ? '' : 'read';
                    $bg = 'bg'.($i%2);
                    $mb = get_member($row['me_chat_id']);
                    //읽지 않은 메세지
                    $q = "select count(me_id) as cnt from {$g5['memo_table']} where me_send_mb_id='".$row['me_chat_id']."' and me_recv_mb_id='".$member['mb_id']."' and me_read_datetime = '0000-00-00 00:00:00'";
                    $r = sql_fetch($q);
                    $p_icon = get_member_profile_img($mb['mb_id']);
                    
                    echo '<li class="memo-chatroom chat-link" data-mb_id="'.$row['me_chat_id'].'">';
                    echo '<span class="chatroom-icon">'.$p_icon.'</span>';
                    
                    if($r['cnt'] > 0) { echo '<span class="no_read"></span>'; } else { echo '<span class="no_read2"></span>'; }
                    
                    echo '<span class="chatroom-view">';
                    echo '<span class="chatroom-name">'.$mb['mb_nick'].'</span>';
                    echo '<span class="chatroom-title">'.cut_str($row['me_memo'],15).'</span>';
                    echo '<span class="chatroom-date">'.$row['me_send_datetime'].'</span>';
                    echo '</span>';
                    
                    
                    
                    if($r['cnt'] > 0) {
                        echo '<dd class="chatroom-cnt badge2"><span class="cnttxt">신규메세지 </span><span class="cntnum">'.$r['cnt'].'</span></dd>';
                    }
 
                    echo '</li>';

                }
                ?>

                </ul>
            </div>
            
        </div>
    </div>

    

    
    
    <div id="memo-footer">
        
        <strong><?php echo $config['cf_memo_del'] ?></strong>일이 지난 대화는 삭제됩니다.
         
        <a href="javascript:void(0);" id="btn_close3" onclick="javascript:window.location.reload();">
            <i class="fa fa-refresh" aria-hidden="true"></i>
        </a>
    </div>
    
   
</div>



<script>

$(function() {
    $(".chat-link").on('click', function() {
        var $this = $(this),
            $what = $this.closest('[data-mb_id]');
            value = $what.data('mb_id');
        var href = "./memo_form.php?me_recv_mb_id="+value;
        var new_win = window.open(href, 'win_'+value, 'left=400,top=50,width=450,height=600,scrollbars=1');
        new_win.focus();
    });
});

function chat_invite() {
	var mb_nick = $("#sch_stx").val();
	if( mb_nick == "" )
	{	
		alert( "추가하실 회원의 닉네임을 입력하세요." );
		$("#sch_stx").focus();
        return false;
	}
	$.ajax({
        type: "POST",
        data: {act:'search_member',mb_nick:mb_nick},
        url: '<?php echo G5_BBS_URL; ?>/ajax.memo.php',
        success: function(data) {
            var html = '';
            $.each(data, function(i, $i) {
                if (!$i) {
                    alert('대화상대를 추가하지 못하였습니다. 닉네임을 정확히 입력하세요.');
                    return false;
                } else {
                    var href = "<?php echo G5_BBS_URL; ?>/memo_form.php?me_recv_mb_id="+$i.mb_id;
                    var new_win = window.open(href, 'win_'+$i.mb_id, 'left=400,top=50,width=450,height=600,scrollbars=1');
                    new_win.focus();
                    return false;
                }
            });
           
        }
    });
    return false;
}

    

    $(function() {    //화면 다 뜨면 시작
        var searchSource = [
            <?php 
            for ($i=0; $row=sql_fetch_array($result); $i++) { 
                if($row['mb_level'] < 10) {
                    echo json_encode($row['mb_nick']).",";
                }
            }
            ?>
        ]; // 배열 형태로 

        $("#sch_stx").autocomplete({  //오토 컴플릿트 시작
            source : searchSource,    // source 는 자동 완성 대상
            select : function(event, ui) {    //아이템 선택시
                console.log(ui.item);
            },
            focus : function(event, ui) {    //포커스 가면
                return false;//한글 에러 잡기용도로 사용됨
            },
            open: function(){
                $('.ui-autocomplete').css('width', '100%');
                $('.ui-autocomplete').css('top', '60px');
                $('.ui-autocomplete').css('left', '0px');
                $('.ui-autocomplete').css('font-size', '12px');
                $('.ui-autocomplete').css('border', '0px');
                $('.ui-autocomplete').css('background-color', '#fff');
                $('.ui-autocomplete').css('max-height', '190px');
                $('.ui-autocomplete').css('overflow-y', 'scroll');
                $('.ui-autocomplete').css('overflow-x', 'hidden');
                $('.ui-autocomplete').css('border-bottom', '1px solid #eee');
                $('.ui-autocomplete').css('box-shadow', '10px 0px 10px rgba(0,0,0,0.1)');
                $('.ui-autocomplete').css('box-sizing', 'border-box');
                $('.ui-menu-item-wrapper').css('padding', '10px 10px 10px 10px');
                $('.ui-menu-item-wrapper').css('border', '0px');
                $('.ui-state-active').css('background', '#f9f9f9');
                $('.ui-state-active').css('font-weight', 'bold');
            },
            minLength: 1,// 최소 글자수
            autoFocus: true, //첫번째 항목 자동 포커스 기본값 false
            classes: {    //잘 모르겠음
                "ui-autocomplete": "highlight"
            },
            delay: 500,    //검색창에 글자 써지고 나서 autocomplete 창 뜰 때 까지 딜레이 시간(ms)
//            disabled: true, //자동완성 기능 끄기
            position: { my : "right top", at: "right bottom" },    //잘 모르겠음
            close : function(event){    //자동완성창 닫아질때 호출
                console.log(event);
            }
            
            
        });
        
    });
    

/*
setInterval( function() {
    location.reload();	
}, 20000 ); //20초에 갱신
*/

</script>