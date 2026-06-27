# Agent Runtime Evidence — PulseDesk (2026-06-27)

## Hermes (orchestrator) — live on EastRouter, Slack connected
```
  Z.AI / GLM       ✗ not configured (run: hermes model)
  Kimi / Moonshot  ✗ not configured (run: hermes model)
  StepFun Step Plan ✗ not configured (run: hermes model)
  MiniMax          ✗ not configured (run: hermes model)
  MiniMax (China)  ✗ not configured (run: hermes model)
◆ Messaging Platforms
  Telegram      ✗ not configured
  Discord       ✗ not configured
gateway service: active
model:
model:
  default: z-ai/glm-5.1
  provider: custom
  base_url: https://api.eastrouter.com/v1
```

## OpenClaw (coder) — EastRouter provider
```
default model + provider:
provider eastrouter: {'baseUrl': 'https://api.eastrouter.com/v1', 'api': 'openai-completions'}
models: ['z-ai/glm-5.1', 'z-ai/glm-4.5-air', 'moonshotai/kimi-k2.7-code']
primary: eastrouter/z-ai/glm-5.1
workspace: /home/aman/PulseDesk
```

## Backend tests (Pest/PHPUnit)
```
  ✓ ticket filter and search                                             0.01s  
  ✓ customer reply cannot be internal                                    0.01s  

  Tests:    9 passed (24 assertions)
  Duration: 0.30s

```

## Live services
```
0 pulsedesk-api fork
1 pulsedesk-web fork
live web  -> 200
live api  -> 200
```
