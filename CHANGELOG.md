# Changelog

## [0.3.4] - 2026-07-20

### Fixed

- 모듈 관리 UI에서 내부 식별자 대신 locale 기반 제품 표시명을 사용하도록 수정

## [0.3.3] - 2026-07-19

### Added

- 관리자 메뉴에 운영 브리핑, 신호, 사건, 감사 기록, 분석 규칙 하위 탐색 추가
- 관리자 화면·라우트·권한 계약 테스트 보강

### Changed

- 관리자 메뉴 문구 및 다국어 리소스 정리
- README와 모듈 버전 정보 정합성 수정

## [0.3.2] - 2026-07-19

### Fixed

- 분석 규칙 화면에서 최근 설정 시각이 올바르게 표시되지 않던 문제 수정
- 설정 이력이 없는 규칙에 안내 문구를 표시하도록 개선

## [0.3.1] - 2026-07-19

### Fixed

- 통계가 없는 경우와 0건 통계를 명확히 구분하도록 개선
- 관리자 화면의 신호·사건·검토 관련 한국어 표현을 통일
- 빈 상태에서 원시 데이터 표현이 노출되지 않도록 개선

## [0.3.0] - 2026-07-19

### Added

- 운영 브리핑 통계를 목록형 UI로 표시하고 Signal 검토 입력, 민감 근거 수동 조회, Incident Timeline 운영 화면을 완성
- Signal, Incident, Timeline, Audit, Rule 화면에 일관된 Loading, Empty, Error, Success 상태와 접근성 피드백 추가

### Changed

- Timeline이 동일 시각 이벤트도 생성 순서대로 안정적으로 표시되도록 정렬 계약 보강


## [0.2.2] - 2026-07-19

### Fixed

- 관리자 layout API endpoint를 실제 모듈 identifier 기반 underscore prefix와 일치시켜 전체 운영 API의 404를 수정
- 관리자 layout과 routes의 번역 namespace를 실제 language catalog의 underscore namespace로 통일
- 브리핑 API 실패 시 fallback 0 통계를 숨기고 명시적 오류 상태만 표시

## [0.2.1] - 2026-07-19

### Fixed

- 관리자 레이아웃 권한 namespace를 모듈 identifier 기반 underscore 형식으로 통일해 기존 Role 권한과의 403 불일치를 수정
- 기존 0.2.0 설치본도 공식 module update의 권한·Role·레이아웃 멱등 동기화로 보정

## [0.2.0] - 2026-07-19

### Added

- Signal 검토에서 Incident 생성·상태 전이·시간순 Timeline·운영 감사까지 연결
- 신규 신호, 미검토, 진행 중 사건, 최근 검토, 규칙·우선순위 통계를 제공하는 운영 브리핑
- 신호 상세 검토, Incident 목록·Timeline, 감사 로그 관리자 레이아웃

### Changed

- 관리자 조회를 IntelligenceQueryService로 모아 Controller의 직접 Model query 제거
- Review 상태 전이를 명시적으로 제한하고 terminal 상태를 보호

## [0.1.2] - 2026-07-19

### Fixed

- DB 테이블 접두사가 설정된 운영 환경에서도 신고 집중도 분석이 정상 실행되도록 개선

## [0.1.1] - 2026-07-19

### Fixed

- 관리자 API가 사건, 규칙, 열람 감사 데이터를 명시적 리소스로 제한해 반환하도록 개선
- 신규 모듈 테이블의 스키마 설명을 보강해 운영 감사 가능성을 개선

## [0.1.0] - 2026-07-19

### Added

- 세 가지 설명 가능한 운영 신호와 제한된 분석 명령
- 신호, 관찰, 사건, 사건 이벤트, 검토, 규칙, 열람 감사 모델
- 관리자 API, 권한, 메뉴, 번역 기반 JSON 레이아웃
- 개인정보·운영·API·아키텍처 문서와 단위 테스트
