# Domain model

- Rule: 활성, 관찰 기간, threshold, board scope, 변경자와 증가하는 version.
- Signal: 계산 당시 rule version·기간·집계·설명 key를 고정한 파생 결과. status만 검토에 따라 변합니다.
- Observation: 원문 없이 최소 집계와 원본 식별자만 저장합니다.
- Incident/Event: 주 대상을 단순하게 묶고 신호 생성 및 검토 전이를 시간순 기록합니다. 복잡한 자동 병합은 하지 않습니다.
- Review: append-only. `dismissed`는 reason code가 필수입니다.
- AccessLog: 민감 신호 상세 열람과 Review 및 Rule 변경의 사용자, 대상, 기능 식별자, 시각만 기록합니다.

Review 전이는 `unreviewed → reviewing|observing|resolved|dismissed`, `reviewing ↔ observing`, 활성 상태에서 `resolved|dismissed`만 허용합니다. `resolved`와 `dismissed`는 terminal이며 기존 Review 행은 수정하거나 삭제하지 않습니다. Incident는 Signal 생성 시 대상 단위로 생성되고, 생성·Signal 연결·Review 추가·상태 변경·종료 이벤트를 시간순으로 보존합니다.

Signal 중복 키는 신호 종류·대상·rule version·관찰 window로 구성합니다. 규칙 변경은 version을 올리되 기존 Signal을 다시 계산하거나 수정하지 않습니다.
