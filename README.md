# glitter-community-intelligence

- Identifier: `glitter-community_intelligence`
- Vendor: `Glitter.kr`
- Version: `0.3.4`

게시물·댓글·신고의 제한된 시간 구간 집계를 설명 가능한 운영 신호로 바꾸고, 운영자 검토 이력과 사건 타임라인을 보존하는 GnuBoard7 관리자 전용 모듈입니다.

> 이 모듈이 제공하는 신호는 운영 검토를 위한 보조 정보이며 동일인, 부계정, 악성 회원, 공모 또는 제재 필요성을 자동 판정하지 않습니다.

## MVP

- Comment Surge, Report Concentration, Stale Report
- 브리핑, 신호/사건 조회, append-only 검토 이력, 규칙 설정, 민감 상세 열람 감사
- `community-intelligence:analyze`, `community-intelligence:prune` 명령의 범위·건수 제한 및 dry-run
- 관리자 JSON 레이아웃 7종과 관리자 API 10개를 제공합니다. 별도 JavaScript/CSS 자산이나 외부 CDN은 사용하지 않습니다.

Repeated Conflict는 안전한 회원 집합·게시판 범위 권한 계약이 충분하지 않아 기본 규칙만 비활성 상태로 제공하며 계산하지 않습니다. 알림과 Reverb도 핵심 의존성에서 제외합니다.

## 설치·활성화

공식 lifecycle을 통해 설치·활성화하고, bundled 소스 변경은 `/usr/local/bin/php83 artisan module:update glitter-community_intelligence --source=bundled --force --no-interaction`로 반영해야 합니다. 운영 DB에서는 사전 백업과 변경 승인이 필요합니다. 소스 디렉터리를 활성 디렉터리에 직접 복사하거나 수정하지 마십시오.

## 권한

`intelligence.view`, `view_sensitive`, `review`, `configure`, `audit`를 분리합니다. 민감 근거·감사·설정은 기본적으로 최고 관리자 역할에만 부여합니다. 공개 API는 없습니다.

## 분석과 보존

`/usr/local/bin/php83 artisan community-intelligence:analyze --dry-run --json`으로 후보를 먼저 확인합니다. Observation 기본 제안은 90일, access log는 180일이며 prune 옵션으로 변경할 수 있습니다. Signal/Review는 자동 prune하지 않습니다.

## 개인정보 제한

원문, IP, user-agent, session, token, fingerprint, 위치, 비공개 메시지를 복제·수집하지 않습니다. 외부 원본은 느슨한 식별자로만 참조합니다.

## 알려진 한계

- 명령 실행 시점의 제한된 집계만 사용하며 과거 삭제 사건을 완전 재현하지 않습니다.
- 게시판 관리자별 범위 계약을 이 모듈이 안전하게 재사용하지 못해 전역 관리자 권한으로 제한합니다.
- queue 기본값이 sync인 환경에서도 웹 요청은 분석을 실행하지 않습니다.

## 테스트와 빌드

모듈 테스트는 프로젝트 PHPUnit 구성에서 신규 모듈 테스트 경로만 지정해 실행합니다. DB Feature/lifecycle 테스트는 격리된 테스트 DB가 확인된 경우에만 실행합니다. 별도 frontend source와 dist가 없어 frontend build는 필요하지 않습니다.
