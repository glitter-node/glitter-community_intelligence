# Architecture

`AnalysisService → IntelligenceSourceGatewayInterface → BoardIntelligenceSourceGateway` 경계로 외부 게시판 구현을 격리합니다. 게시판 공개 Repository들은 bounded aggregate 질의를 제공하지 않으므로 어댑터 안에서 공개 Module Model을 사용합니다. 도메인 서비스에는 외부 테이블명이나 모델이 전파되지 않습니다.

웹 요청은 분석을 수행하지 않습니다. 운영자가 제한·기간·게시판·규칙을 지정한 command를 실행합니다. 현재 runtime queue가 `sync`이므로 hook/job을 통해 원본 작성 요청에 분석 실패를 전파하지 않는 보수적 구조입니다.

신호 생성과 Observation/Incident/Event 생성은 단일 transaction이며 해시 키로 멱등성을 확보합니다. 외부 모델에 foreign key/cascade를 만들지 않습니다.
