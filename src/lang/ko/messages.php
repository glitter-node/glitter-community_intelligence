<?php
return [
    'menu' => [
        'root' => '커뮤니티 인텔리전스',
        'briefing' => '운영 브리핑',
        'signals' => '신호',
        'incidents' => '사건',
        'audit' => '감사 기록',
        'settings' => '분석 규칙',
    ],
    'briefing' => ['success' => '운영 브리핑을 조회했습니다.'],
    'signal' => [
        'fetch_success' => '신호를 조회했습니다.',
        'comment_surge' => ['summary' => '댓글 활동이 관찰 기준에 도달했습니다.', 'explanation' => '관찰 기간의 댓글 수가 설정 임계값 이상입니다.'],
        'report_concentration' => ['summary' => '신고 활동이 한 대상에 집중되었습니다.', 'explanation' => '관찰 기간의 신고 수와 서로 다른 신고자 수가 설정 임계값 이상입니다.'],
        'stale_report' => ['summary' => '신고 검토가 설정 시간보다 지연되었습니다.', 'explanation' => '신고 상태가 미처리 범주에 있고 설정 지연 기준을 초과했습니다.'],
    ],
    'review' => ['success' => '검토 이력을 추가했습니다.'],
    'incident' => ['fetch_success' => '사건 타임라인을 조회했습니다.', 'title' => ':type 신호의 사건 :id', 'signal_created' => '인텔리전스 신호가 생성되었습니다.', 'review_changed' => '운영자 검토 상태가 변경되었습니다.'],
    'rule' => ['fetch_success' => '분석 규칙을 조회했습니다.', 'update_success' => '분석 규칙을 변경했습니다.'],
    'audit' => ['fetch_success' => '열람 감사 기록을 조회했습니다.'],
    'command' => ['analyze_complete' => '분석 후보 :count건을 처리했습니다.', 'prune_complete' => '보존 기간이 지난 :count건을 처리했습니다.'],
];
