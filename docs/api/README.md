# Admin API

모든 경로는 `/api/modules/glitter-community_intelligence/admin` prefix, Sanctum 인증, admin permission middleware를 사용합니다.

| Method | Path | Permission |
|---|---|---|
| GET | `/briefing?hours=24` | view |
| GET | `/signals`, `/signals/{id}/summary` | view |
| GET | `/signals/{id}` | view_sensitive |
| POST | `/signals/{id}/reviews` | review |
| GET | `/incidents`, `/incidents/{id}` | view |
| GET, PUT | `/rules`, `/rules/{id}` | configure |
| GET | `/access-logs` | audit |

목록은 pagination을 적용하며 signal sort는 allowlist입니다. 모든 응답은 명시적 Resource로 제한됩니다. Review 응답은 append-only 기록을 반환하고 Incident 상세의 events 배열은 occurred_at 오름차순 Timeline입니다. 민감 Signal 상세, Review, Rule 변경은 access log에 운영 감사 기록을 남깁니다.
