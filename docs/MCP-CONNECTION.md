# MCP pripojenie pre Rezidenciu Lomnica

Tento dokument opisuje pripojenie WordPress MCP Adaptera a Bricks MCP. Prihlasovacie Ăşdaje sa nesmĂş ukladaĹĄ do GitHubu, `.mcp.json`, `.env` ani do WordPress kĂłdu.

## 1. WordPress pluginy

### WordPress MCP Adapter

NainĹˇtaluj oficiĂˇlny release pluginu [WordPress MCP Adapter](https://github.com/WordPress/mcp-adapter/releases/latest), prĂ­padne cez WP-CLI:

```bash
wp plugin install https://github.com/WordPress/mcp-adapter/releases/latest/download/mcp-adapter.zip --activate
```

OficiĂˇlny adapter vyĹľaduje WordPress 6.9+ a PHP 7.4+. PredvolenĂ˝ HTTP endpoint je:

```text
https://WP-DOMENA/wp-json/mcp/mcp-adapter-default-server
```

### Bricks MCP

NainĹˇtaluj release pluginu [Bricks MCP](https://github.com/cristianuibar/bricks-mcp/releases) cez WordPress administrĂˇciu a aktivuj ho. Po aktivĂˇcii skontroluj `Settings > Bricks MCP`:

- MCP Server: zapnutĂ˝,
- Require Authentication: zapnutĂ©,
- Dangerous Actions: vypnutĂ© poÄŤas prvĂ©ho testu,
- Custom Base URL: vyplĹ iba pri reverse proxy alebo vlastnej MCP URL.

Bricks MCP vyĹľaduje WordPress 6.4+, PHP 8.2+ a Bricks 1.6+. Jeho endpoint je:

```text
https://WP-DOMENA/wp-json/bricks-mcp/v1/mcp
```

## 2. Overenie prĂ­stupu

PouĹľi WordPress Application Password pouĹľĂ­vateÄľa s najmenĹˇĂ­mi potrebnĂ˝mi oprĂˇvneniami. PoskytnutĂ˝ Base64 Ăşdaj patrĂ­ iba do lokĂˇlneho `Authorization` headera; do repozitĂˇra sa nekopĂ­ruje.

Header mĂˇ tvar:

```text
Authorization: Basic BASE64_ENCODED_CREDENTIALS
```

Pred pouĹľitĂ­m treba doplniĹĄ skutoÄŤnĂş domĂ©nu novej WordPress inĹˇtalĂˇcie namiesto `WP-DOMENA`.

## 3. KonfigurĂˇcia Bricks MCP klienta

PrĂ­klad pre Claude Desktop, Cursor alebo inĂ˝ HTTP MCP klient:

```json
{
  "mcpServers": {
    "bricks-mcp": {
      "type": "http",
      "url": "https://WP-DOMENA/wp-json/bricks-mcp/v1/mcp",
      "headers": {
        "Authorization": "Basic BASE64_ENCODED_CREDENTIALS"
      }
    }
  }
}
```

Po pripojenĂ­ otestuj najprv `get_site_info` a `get_builder_guide`. Zapisovacie operĂˇcie a sprĂˇvu globĂˇlnych nastavenĂ­ povoÄľ aĹľ po kontrole vĂ˝sledkov a zĂˇlohe WordPressu.

## 4. KonfigurĂˇcia oficiĂˇlneho WordPress MCP Adaptera

Pre HTTP pripojenie cez lokĂˇlny proxy proces pouĹľije klient konfigurĂˇciu v tomto tvare:

```json
{
  "mcpServers": {
    "wordpress-mcp": {
      "command": "npx",
      "args": [
        "-y",
        "@automattic/mcp-wordpress-remote@latest"
      ],
      "env": {
        "WP_API_URL": "https://WP-DOMENA/wp-json/mcp/mcp-adapter-default-server",
        "LOG_FILE": "C:/path/to/mcp-adapter.log",
        "WP_API_USERNAME": "WP_APPLICATION_USERNAME",
        "WP_API_PASSWORD": "WP_APPLICATION_PASSWORD"
      }
    }
  }
}
```

Proxy konfigurĂˇcia pouĹľĂ­va pouĹľĂ­vateÄľskĂ© meno a Application Password samostatne. Ak je k dispozĂ­cii iba Base64 Basic credential, nepĂ­Ĺˇ ho do sĂşboru; najprv si bezpeÄŤne priprav lokĂˇlnu konfigurĂˇciu podÄľa klienta.

## 5. PoznĂˇmka ku Codex/ChatGPT

Bricks MCP v aktuĂˇlnej dokumentĂˇcii uvĂˇdza podporu pre Claude Code, Claude Desktop, Cursor a kompatibilnĂ˝ch MCP klientov, nie priamu podporu ChatGPT. Preto je tento dokument urÄŤenĂ˝ na konfigurĂˇciu podporovanĂ©ho externĂ©ho MCP klienta. SamotnĂ© napojenie v tomto pracovnom prostredĂ­ vyĹľaduje URL novej WordPress inĹˇtalĂˇcie a klienta, ktorĂ˝ povoÄľuje vlastnĂ© HTTP MCP servery.