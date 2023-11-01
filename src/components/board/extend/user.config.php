<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가;

// ./board/extend/user.config.php 파일에 내용 추가
function get_memo_not_read($mb_id) //수신자가 읽지 않은 쪽지의 갯수를 반환해주는 함수.
{
    global $g5; //전역 변수 g5 선언. 이 변수는 쪽지 테이블(memo_table)을 나타낸다.
    $sql = " SELECT count(*) as cnt FROM {$g5['memo_table']} WHERE me_recv_mb_id = '$mb_id' and me_read_datetime like '0%' ";
/*SQL 쿼리를 만든다. 본 쿼리는 쪽지 테이블에서 me_recv_mb_id(쪽지를 받은 수신자 ID)가 $mb_id(인자로 전달된 수신자 ID)인 경우와
 me_read_datetime(쪽지를 읽은 시간)이 '0%'(0으로 시작하는)인 경우를 검색함. 이 조건은 읽지 않은 쪽지를 찾기 위한 것이다.
 이에 따라서, 읽지 않은 쪽지의 개수를 세는 count(*)를 실행함.*/                                                                                                                                                                                                            

    $row = sql_fetch($sql, false); /*위에서 만든 SQL 쿼리를 실행하여 결과를 도출함. sql_fetch() 함수를 사용해 쿼리의 결과를 가져온다.
                                     두 번째 인자 false는 간편한 결과를 위해, 단일 행 결과를 가져오기 위한 옵션.*/

    return $row['cnt']; //쿼리 결과는 cnt 필드로부터 읽지 않은 쪽지의 갯수를 반환함.
}