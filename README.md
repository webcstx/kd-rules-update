# 证书代签 · Let's Encrypt ACME（DNS-01）

一个纯静态网页工具：在浏览器里完成 Let's Encrypt 的 ACME v2 签发流程——建单、生成 DNS TXT 记录、验证域名、签发证书，全程不用服务器、不用命令行。

在线地址：<https://webcat.bbroot.com/>

---

## 这个仓库里到底有什么

| 路径 | 内容 |
| --- | --- |
| `docs/` | GitHub Pages 的发布目录（分支 `豆包`，Pages 源设为 `/docs`） |
| `docs/index.html` | 站点首页：证书代签页面（ACME DNS-01 全流程） |
| `docs/分发证书/index.html` | 同内容的子页面副本，`/分发证书/` 可直接访问 |
| `docs/assets/` | 页面用图：`hero.jpg`（浅色横幅）、`cloudflare.png` / `cloudflare.webp`（图标） |
| `docs/CNAME` | 自定义域名 `webcat.bbroot.com` |
| `docs/.nojekyll` | 关闭 Jekyll 处理，保证文件原样发布 |
| `docs/forge.min.js`、`docs/分发证书/forge.min.js` | 早期本地生成版本留下的库文件，当前页面已不再引用 |
| `docs/GKD-subscription.json3`、`GKD-subscription.json3` | GKD 订阅规则文件（含 `docs` 内外两份，体积约 1.1 MB） |
| `php/` | 一组单文件 PHP 小工具：Base64 编解码、大小写转换、数字转中文、密码生成、随机语录、文本反转、时间换算 |
| `php/acme/` | 引入 [skoerfgen/ACMECert](https://github.com/skoerfgen/ACMECert) v3.7.3 源码（MIT，见该目录 `LICENSE.md`），供服务器侧走 ACME v2 |
| `python/` | 与 `php/` 对应的 Python 版小工具（同样的 7 个功能） |
| `live2.html` | 早期临时页面，未被站点引用 |
| `LICENSE` | Apache License 2.0 |

---

## 页面能做什么

- 输入一个域名（自动去掉 `http(s)://`、路径和端口），点「提交申请」即向 Let's Encrypt 建单
- 页面会算出并显示一条 DNS 记录：主机记录 `_acme-challenge`、类型 `TXT`、记录值一串随机串，带复制按钮
- 把记录添加到 DNS 后，点「我已完成解析，开始验证」，页面自己完成 challenge 提交、轮询授权状态、finalize、下载证书
- 签发成功后显示颁发者、域名、到期时间，证书与私钥都保存在浏览器本地（`localStorage`），不上传任何服务器
- 已签发、待验证的订单状态会存下来，关掉页面再打开还能接着往下走

**有效期由 Let's Encrypt 决定**，一般是 90 天，页面不能自选到期日。

## 人机验证

页面接入了 Cloudflare Turnstile。未通过验证时：

- 「提交申请」按钮保持禁用，点不动；
- 即使绕过按钮直接调用，代码也会在发请求前拦下并提示先完成验证。

站点密钥写在前端（`data-sitekey="0x4AAAAAAF_m03XPKegNGNal"`），密钥本身不参与签发逻辑。

> 注意：该 sitekey 需要在 Cloudflare 后台把 `webcat.bbroot.com` 加入对应 widget 的 Hostname Management，否则组件会报 `110200 Domain not authorized` 并渲染失败。

## 已知限制

- **Let's Encrypt 频率限制**：同一根域 168 小时内最多 50 张。超限时页面会把原文错误展示出来（含 `retry after ... UTC` 时间），这段时间内该根域下所有子域都签不了
- 浏览器直连 ACME 需要页面处于 HTTPS（或 `localhost`），否则 `crypto.subtle` 不可用
- 页面不做 DNS 预检，TXT 没生效时验证会失败，页面会提示「验证未通过」
- 通配符域名（`*.example.com`）走 DNS-01 理论可用，但需要为同一个 `_acme-challenge` 记录同时放多条 TXT；当前页面一次只处理单域名

## 使用步骤

1. 打开 <https://webcat.bbroot.com/>，通过人机验证
2. 填域名（例如 `webcat.bbroot.com`），点「提交申请」
3. 按页面给出的主机记录与记录值，去 DNS 服务商添加 TXT
4. 等解析生效后，点「我已完成解析，开始验证」
5. 签发成功后，在浏览器里取回证书与私钥，配置到 Nginx / Apache

## 部署说明

本仓库用 GitHub Pages 发布：

- 发布分支：`豆包`，目录 `/docs`
- 自定义域名：`webcat.bbroot.com`（`CNAME` 记录指向 `webcstx.github.io`）
- 站点自身的 HTTPS 证书由 GitHub Pages 自动申请（Let's Encrypt），不依赖页面里的功能

## 许可证

[Apache License 2.0](LICENSE)。`php/acme/` 目录内的源码为 MIT 许可，版权归 Stefan Körfgen。

## 作者

webcatx

## Star History

<a href="https://www.star-history.com/?repos=webcstx%2Fdoubao-quick-tools&type=date&legend=top-left">
 <picture>
   <source media="(prefers-color-scheme: dark)" srcset="https://api.star-history.com/chart?repos=webcstx/doubao-quick-tools&type=date&theme=dark&legend=top-left" />
   <source media="(prefers-color-scheme: light)" srcset="https://api.star-history.com/chart?repos=webcstx/doubao-quick-tools&type=date&legend=top-left" />
   <img alt="Star History Chart" src="https://api.star-history.com/chart?repos=webcstx/doubao-quick-tools&type=date&legend=top-left" />
 </picture>
</a>
