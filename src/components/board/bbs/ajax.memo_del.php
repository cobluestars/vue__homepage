<?php
include_once('./_common.php');
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가



if ($member['mb_id']) {

	if($me_id == "") {
		alert('올바른 방법으로 이용해 주세요.');
	} else { 
        
        $sql = " select * from {$g5['memo_table']} where me_id = '{$me_id}' ";
        $row = sql_fetch($sql);

		$sql = " delete from {$g5['memo_table']} where me_id = '{$me_id}' and me_send_mb_id = '{$member['mb_id']}' and me_recv_mb_id = '{$del_id}' ";
		sql_query($sql);
        
        if (!$row['me_read_datetime'][0]) // 메모 받기전이면
        {
            $sql = " update {$g5['member_table']}
                        set mb_memo_call = ''
                        where mb_id = '{$row['me_recv_mb_id']}'
                        and mb_memo_call = '{$row['me_send_mb_id']}' ";
            sql_query($sql);

            $sql = " update `{$g5['member_table']}` set mb_memo_cnt = '".get_memo_not_read($del_id)."' where mb_id = '$del_id' ";
            sql_query($sql);
        }
        
		alert('대화가 삭제 되었습니다.');
	}

} else { 
    alert_close('회원만 이용하실 수 있습니다.');
}

?>
