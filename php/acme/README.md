# php/acme · 用 PHP 向公共 CA 申请证书

这个目录是给“想用 PHP 自动向公共 CA（Let's Encrypt）申请可信证书”的场景用的，与站点页面里的自建 CA 本地自签是两条完全不同的路。

## 第三方代码来源

- 项目：`skoerfgen/ACMECert`
- 说明：PHP 的 ACME v2（RFC 8555）客户端库，支持 `http-01`、`dns-01`、`tls-alpn-01` 三种验证
- 许可证：MIT（见同目录 `LICENSE.md`，版权归 Stefan Körfgen）
- 收录文件：`ACMECert.php`、`src/ACMECert.php`、`src/ACMEv2.php`、`src/ACME_Exception.php`
- 上游地址：https://github.com/skoerfgen/ACMECert

本项目仓库整体是 Apache-2.0，MIT 与 Apache-2.0 兼容，保留原版权与许可声明即可。

## 本仓库自己写的部分

`issue.php` 是命令行封装（不是第三方代码），只调用上面的库，流程为：注册账号 → 生成证书密钥 → dns-01 验证 → 写出证书。

```bash
php issue.php --domain example.com --email you@example.com
php issue.php --domain example.com --domain '*.example.com' --ca staging
```

产物：

```
fullchain.pem   证书链（Nginx 的 ssl_certificate、Apache 的 SSLCertificateFile 直接用这个）
privkey.pem     证书私钥
account_key.pem ACME 账号密钥，务必保留，丢了要重新注册
cert_key.pem    证书私钥（复用它续期，保持公钥不变）
```

## 放行前提（顺序不能反）

1. **CAA 放行**：根域要允许 Let's Encrypt
   ```
   bbroot.com  CAA  0 issue "letsencrypt.org"
   ```
   已经设置成 CNAME 的域名不能再挂 CAA（DNS 记录互斥），所以要写在根域上；CA 会从子域逐级向上找最近的一条。

2. **域名可验证**：dns-01 要求 `_acme-challenge.你的域名` 下能写 TXT。不想把整域 DNS 权限交出去，可以做一次性委托：
   ```
   _acme-challenge.example.com.  CNAME  你的委托名.acme-dns.example.org.
   ```
   这条 CNAME 长期不变，每次续期只改委托目标那边的 TXT 内容。

## 已知限制

- 通配符证书只能用 `dns-01`
- 公共 CA 的有效期不可自定义（Let's Encrypt 现为固定短期证书）
- 账号密钥、证书私钥不要提交到仓库；本目录没有 `.gitignore` 规则，使用时自行留意
- 需要 PHP 的 `openssl` 扩展，并允许向外访问 ACME 目录地址
