# 证书代签 · Let's Encrypt ACME（DNS-01）

一个纯静态网页工具：在浏览器里完成 Let's Encrypt 的 ACME v2 签发流程——建单、生成 DNS TXT 记录、验证域名、签发证书，全程不用服务器、不用命令行。


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

## Star History

<a href="https://www.star-history.com/?repos=webcstx%2Fdoubao-quick-tools&type=date&legend=top-left">
 <picture>
   <source media="(prefers-color-scheme: dark)" srcset="https://api.star-history.com/chart?repos=webcstx/doubao-quick-tools&type=date&theme=dark&legend=top-left" />
   <source media="(prefers-color-scheme: light)" srcset="https://api.star-history.com/chart?repos=webcstx/doubao-quick-tools&type=date&legend=top-left" />
   <img alt="Star History Chart" src="https://api.star-history.com/chart?repos=webcstx/doubao-quick-tools&type=date&legend=top-left" />
 </picture>
</a>
