# Operations

1. 격리된 환경에서 공식 install/activate lifecycle을 수행합니다.
2. `community-intelligence:analyze --dry-run --hours=24 --limit=500 --json`으로 후보 수를 확인합니다.
3. 필요하면 `--rule=comment_surge`, `--board=ID`로 범위를 제한해 실행합니다.
4. `community-intelligence:prune --dry-run --json` 후 Observation 90일/access log 180일 기본 제안에 따라 batch 정리합니다.

명령은 재실행 가능하며 이미 존재하는 동일 window 신호를 만들지 않습니다. 한 source 질의가 실패하면 non-zero로 종료되고 원본 게시판 처리는 영향을 받지 않습니다. 현재 구현은 source별 오류 격리 재시도 queue를 제공하지 않습니다.

운영자는 브리핑에서 미검토 Signal을 확인하고 Signal 상세에서 검토를 시작하거나 종료할 수 있습니다. 검토는 Incident 상태와 Timeline을 갱신하며 Audit 화면에서 민감 조회·검토·규칙 변경 기록을 확인합니다. terminal Review는 되돌릴 수 없으므로 잘못 종료한 경우 원본 데이터를 변경하지 말고 별도의 운영 보정 절차를 사용합니다.
