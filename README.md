# Brasileirão API

Bem-vindo à Brasileirão API! Esta API fornece informações atualizadas sobre o Campeonato Brasileiro de Futebol, incluindo classificação, resultados de rodadas e notícias relacionadas.

## Índice

- [Instalação](#instalação)
- [Uso](#uso)
- [Rotas Disponíveis](#rotas-disponíveis)
- [Exemplos](#exemplos)
- [Contribuição](#contribuição)
- [Licença](#licença)

## Instalação

1. Clone o repositório:
   ```
   git clone https://github.com/kage3f/brasileirao-api.git
   ```

2. Entre no diretório do projeto:
   ```
   cd brasileirao-api
   ```

3. Instale as dependências:
   ```
   composer install
   ```

4. Copie o arquivo de ambiente e configure suas variáveis:
   ```
   cp .env.example .env
   ```

5. Gere a chave da aplicação:
   ```
   php artisan key:generate
   ```

6. Inicie o servidor de desenvolvimento:
   ```
   php artisan serve
   ```

A API agora deve estar rodando em `http://localhost:8000`.

## Uso

A API oferece várias rotas para acessar diferentes tipos de informações sobre o Brasileirão. Todas as respostas são retornadas no formato JSON.

## Rotas Disponíveis

1. **Classificação**
   - Rota: `/api/brasileirao/classificacao`
   - Método: GET
   - Descrição: Retorna a classificação atual do Brasileirão.

2. **Rodada**
   - Rota: `/api/brasileirao/rodada`
   - Método: GET
   - Parâmetros opcionais:
     - `rodada`: Número da rodada específica (padrão: rodada atual)
   - Descrição: Retorna os jogos de uma rodada específica ou da rodada atual.

3. **Notícias**
   - Rota: `/api/brasileirao/news`
   - Método: GET
   - Parâmetros opcionais:
     - `page`: Número da página (padrão: 1)
     - `per_page`: Número de notícias por página (padrão: 20)
   - Descrição: Retorna as últimas notícias relacionadas ao Brasileirão.

## Exemplos

### Obter a classificação atual

```
GET /api/brasileirao/classificacao
```

Resposta:
```json
{
  "success": true,
  "data": [
    {
      "posicao": "1°",
      "nome": "Botafogo",
      "sigla": "BOT",
      "pontos": 46,
      "jogos": 23,
      "vitorias": 14,
      "empates": 4,
      "derrotas": 5,
      "saldoGols": 17
    },
    // ... outros times
  ]
}
```

### Obter jogos de uma rodada específica

```
GET /api/brasileirao/rodada?rodada=5
```

Resposta:
```json
{
  "success": true,
  "data": {
    "rodada": 5,
    "jogos": [
      {
        "date": "17/08 16:00 • Arena MRV",
        "home_team": "CAM",
        "away_team": "CUI",
        "home_score": "1",
        "away_score": "1"
      },
      // ... outros jogos
    ]
  }
}
```

### Obter notícias recentes

```
GET /api/brasileirao/news?page=1&per_page=5
```

Resposta:
```json
{
  "success": true,
  "data": [
    {
      "title": "Internacional realiza promoção para lotar o Beira-Rio contra o Cruzeiro pelo Brasileirão",
      "category": "Brasileirão",
      "date": "22/08/2024 - 05:40",
      "link": "https://www.lance.com.br/internacional/...",
      "imageUrl": "https://lncimg.lance.com.br/..."
    },
    // ... outras notícias
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 5,
    "to": 5,
    "total": 5
  }
}
```

## Contribuição

Contribuições são bem-vindas! Por favor, sinta-se à vontade para submeter pull requests ou criar issues para melhorias ou correções.

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

---

Desenvolvido por [Tiago Rodrigues](https://www.linkedin.com/in/tiago-rodrigues-laravel/)
