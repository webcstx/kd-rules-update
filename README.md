# Free Issuance SSL 证书分发

在浏览器本地为你的域名生成 SSL 证书，到期日自己选，证书与私钥都在本地产生，页面不向任何服务器提交内容。

在线地址：https://webcat.bbroot.com/

## 功能

- 填写域名，自动写入证书的 SAN 扩展，支持多个域名（逗号或换行分隔）、通配符与 IP
- 到期日直接选：页面上用日期选择器指定失效那天，不按天数、月数往后推算；选好立刻显示「还剩 N 天」
- 不签发、不查 DNS：页面不向任何 CA 提交申请，也不把域名送去 DNS 解析，没有 CAA 查询闸门，填域名点一下就在本地出证
- 证书名称统一为英文通用名 `Free Issuance`，域名只写在 SAN 里
- 支持用口令导出加密的 PKCS#12 容器（`.p12`），内含证书与私钥，适合加密传输
- 证书与私钥不上传任何服务器，关闭页面即清除

## 信任机制

站点自建一张根证书（`CN=Free Issuance Root CA, O=Free Issuance`，有效期 20 年），由它签发站点证书（`CN=Free Issuance, O=Free Issuance`）。

- 把 `rootCA.crt` 装进设备信任列表后，本站生成的证书即被系统信任，到期日不受公共 CA 限制
- 安卓：设置 → 安全 → 加密与凭据 → 安装证书 → CA 证书
- Windows：双击导入，证书存储选「受信任的根证书颁发机构」
- iOS：用描述文件安装，再到「关于本机 → 证书信任设置」开启完全信任

根证书在本机浏览器里只生成一次，之后一直复用同一张，页面没有重置入口。想换新根证书只能清掉该站点的浏览器数据（`localStorage`）后重新打开。

注意：自建根证书只对装过它的设备生效；没装过的设备仍会提示证书不受信任。要让所有人默认信任，只能使用公共 CA 签发的证书，而公共 CA 的有效期不可自定义。

## 在服务器上启用 HTTPS

生成成功后，页面结果区会直接给出 Nginx 与 Apache 两段配置，域名自动替换成你填的域名，可一键复制。

先上传证书（示例路径 `/etc/ssl/webcat/`）：

```
fullchain.pem → /etc/ssl/webcat/fullchain.pem
cert.pem      → /etc/ssl/webcat/cert.pem
key.pem       → /etc/ssl/webcat/key.pem
```

Nginx 关键配置：

```
server {
    listen 443 ssl;
    server_name 你的域名;

    ssl_certificate     /etc/ssl/webcat/fullchain.pem;
    ssl_certificate_key /etc/ssl/webcat/key.pem;
    ssl_protocols       TLSv1.2 TLSv1.3;

    location / {
        root  /var/www/html;
        index index.html;
    }
}

server {
    listen 80;
    server_name 你的域名;
    return 301 https://$host$request_uri;
}
```

Apache 关键配置：

```
<VirtualHost *:443>
  ServerName 你的域名
  SSLEngine on
  SSLCertificateFile    /etc/ssl/webcat/fullchain.pem
  SSLCertificateKeyFile /etc/ssl/webcat/key.pem
  DocumentRoot /var/www/html
</VirtualHost>
```

改完先自检再重载：

```
nginx -t && nginx -s reload
apachectl configtest && systemctl reload httpd
```

前提仍然是客户端装过 `rootCA.crt`，否则浏览器会提示证书不受信任。

## 关于 CAA

CAA（RFC 8659）是 DNS 里声明“哪些公共 CA 可以给本域名签证书”的记录，只有公共 CA 在签发前会查询它：

- 它与本站的自建 CA 无关：页面在本地生成证书，不检查 CAA，也不发任何 DNS 查询
- 它不能替代根证书安装。证书是否被信任，只取决于客户端有没有装 `rootCA.crt`
- `0 issue ";"` 的含义是“不允许任何 CA 签发”，不是“宽松放行”
- `issue` 的值只能是 CA 域名，写成网址或路径属于非标准用法

如果域名同时用 GitHub Pages 托管，证书由 Let's Encrypt 签发，配置 CAA 时必须保留 `letsencrypt.org`，否则到期续签会被拒绝。

## 使用方式

1. 打开站点，域名框已预填当前域名，可改成你要的域名或多个域名
2. 在「到期日期」里直接选失效那天，页面显示还剩多少天
3. 点「生成证书」
4. 页面立即生成证书与私钥，并显示颁发机构与失效时间
5. 下载 `rootCA.crt` 装进设备，再下载 `cert.pem` / `fullchain.pem` / `key.pem`，或用口令导出加密的 `.p12`
6. 结果区还会给出 Nginx / Apache 的 HTTPS 配置，复制到服务器重载即可开启 HTTPS

## 附带的 PHP 签发库

`php/acme/` 直接引入开源项目 [skoerfgen/ACMECert](https://github.com/skoerfgen/ACMECert) v3.7.3 的源码（MIT 许可，版权归 Stefan Körfgen，许可证见该目录 `LICENSE.md`），用于让服务器自己走 ACME v2 向公共 CA 申请证书，支持 `http-01`、`dns-01`、`tls-alpn-01`。

它与本站页面里用自建根证书在本地生成的证书无关：一个是公共可信证书，有效期由 CA 决定；一个是自建证书，到期日自选，但只有装过根证书的设备才信任。

使用前 CAA 需放行：根域加 `0 issue "letsencrypt.org"`（已成 CNAME 的域名不能再挂 CAA）。

## 许可证

本项目采用 [Apache License 2.0](LICENSE)。

## 作者

webcatx
