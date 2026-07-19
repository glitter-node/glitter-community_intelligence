# Permissions

| Permission | Scope |
|---|---|
| `glitter-community_intelligence.intelligence.view` | 브리핑, 일반 신호, 사건 목록 |
| `glitter-community_intelligence.intelligence.view_sensitive` | Observation과 검토 이력이 포함된 신호 상세 및 열람 기록 생성 |
| `glitter-community_intelligence.intelligence.review` | 상태 전이와 append-only 검토 메모 |
| `glitter-community_intelligence.intelligence.configure` | 규칙 조회·변경과 version 증가 |
| `glitter-community_intelligence.intelligence.audit` | 민감 상세 열람 감사 조회 |

공식 게시판별 관리자 scope를 이 모듈의 외부 aggregate에 안전하게 적용하는 공개 계약이 확인되지 않아 전역 admin middleware와 모듈 권한을 사용합니다. 일반 회원 API는 없습니다.
